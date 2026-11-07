<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CatExamController extends Controller
{
    /**
     * 1. Menampilkan Halaman Penjelasan Tryout & Mesin CAT
     */
    public function show($id)
    {
        $user = Auth::user() ?? DB::table('users')->first();

        $tryoutQuery = DB::table('tryouts')->where('tryouts.id', $id);

        if (Schema::hasColumn('tryouts', 'program_id')) {
            $tryoutQuery->leftJoin('programs', 'tryouts.program_id', '=', 'programs.id')
                ->select('tryouts.*', 'programs.name as program_name');
        }

        $tryout = $tryoutQuery->first();

        if (!$tryout) {
            return redirect('/dashboard')->with('error', 'Paket Tryout tidak ditemukan.');
        }

        // Ambil aturan urutan kategori & kuota soal jika tabel tryout_category_rules ada
        $rules = collect();
        if (Schema::hasTable('tryout_category_rules')) {
            $rules = DB::table('tryout_category_rules')
                ->where('tryout_id', $id)
                ->orderBy('sort_order', 'asc')
                ->get();
        }

        // Cek sesi ujian yang masih aktif (finished_at IS NULL) dan waktunya belum habis
        $activeAttempt = DB::table('tryout_attempts')
            ->where('user_id', $user->id)
            ->where('tryout_id', $id)
            ->whereNull('finished_at')
            ->orderByDesc('id')
            ->first();

        if ($activeAttempt) {
            $startedAt = Carbon::parse($activeAttempt->started_at);
            $endTime = $startedAt->copy()->addMinutes($tryout->duration_minutes ?: 30);

            // Jika sesi lama sudah lewat batas waktu, otomatis tutup sesi tersebut
            if (Carbon::now()->greaterThanOrEqualTo($endTime)) {
                DB::table('tryout_attempts')
                    ->where('id', $activeAttempt->id)
                    ->update([
                        'finished_at' => $endTime,
                        'updated_at'  => now(),
                    ]);
                $activeAttempt = null;
            }
        }

        return view('dashboard.cat-exam', compact('user', 'tryout', 'rules', 'activeAttempt'));
    }

    /**
     * 2. Konfirmasi Mulai Ujian: Buat Attempt, Acak Soal per Kategori, & Mulai Timer
     */
    public function startExam(Request $request, $id)
    {
        $user = Auth::user() ?? DB::table('users')->first();
        $tryout = DB::table('tryouts')->where('id', $id)->firstOrFail();
        $now = Carbon::now();

        // Cek apakah ada attempt yang sedang berjalan (finished_at IS NULL)
        $attempt = DB::table('tryout_attempts')
            ->where('user_id', $user->id)
            ->where('tryout_id', $id)
            ->whereNull('finished_at')
            ->orderByDesc('id')
            ->first();

        // Jika ada attempt menggantung tapi waktunya sudah habis, tutup dulu
        if ($attempt) {
            $startedCheck = Carbon::parse($attempt->started_at);
            $endCheck = $startedCheck->copy()->addMinutes($tryout->duration_minutes ?: 30);
            if ($now->greaterThanOrEqualTo($endCheck)) {
                DB::table('tryout_attempts')
                    ->where('id', $attempt->id)
                    ->update(['finished_at' => $endCheck, 'updated_at' => $now]);
                $attempt = null;
            }
        }

        if (!$attempt) {
            $selectedQuestionIds = [];

            // A. Jika ada tabel tryout_category_rules & tryout_questions, acak sesuai kuota per kategori
            if (Schema::hasTable('tryout_category_rules') && Schema::hasTable('tryout_questions')) {
                $rules = DB::table('tryout_category_rules')
                    ->where('tryout_id', $id)
                    ->orderBy('sort_order', 'asc')
                    ->get();

                foreach ($rules as $rule) {
                    $query = DB::table('tryout_questions')
                        ->join('questions', 'tryout_questions.question_id', '=', 'questions.id')
                        ->where('tryout_questions.tryout_id', $id)
                        ->where('tryout_questions.category', $rule->category);

                    if ($rule->is_randomized ?? true) {
                        $query->inRandomOrder();
                    } else {
                        $query->orderBy('tryout_questions.question_number', 'asc');
                    }

                    $catIds = $query->limit($rule->display_quota)
                        ->pluck('questions.id')
                        ->toArray();

                    $selectedQuestionIds = array_merge($selectedQuestionIds, $catIds);
                }
            }

            // B. Fallback 1: Ambil dari tabel questions berdasarkan tryout_id
            if (empty($selectedQuestionIds)) {
                $selectedQuestionIds = DB::table('questions')
                    ->where('tryout_id', $id)
                    ->orderBy('id', 'asc')
                    ->pluck('id')
                    ->toArray();
            }

            // C. Fallback 2: Ambil soal dari bank soal umum jika tryout ini belum punya soal spesifik
            if (empty($selectedQuestionIds)) {
                $selectedQuestionIds = DB::table('questions')
                    ->limit($tryout->total_questions ?: 30)
                    ->pluck('id')
                    ->toArray();
            }

            $selectedQuestionIds = array_values(array_unique($selectedQuestionIds));

            // Siapkan data insert ke tryout_attempts sesuai kolom yang ada di database
            $attemptData = [
                'user_id'     => $user->id,
                'tryout_id'   => $id,
                'started_at'  => $now,
                'finished_at' => null,
                'total_score' => 0,
                'is_passed'   => 0,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];

            if (Schema::hasColumn('tryout_attempts', 'question_snapshot_ids')) {
                $attemptData['question_snapshot_ids'] = json_encode($selectedQuestionIds);
            }

            $attemptId = DB::table('tryout_attempts')->insertGetId($attemptData);

            // Inisialisasi baris jawaban di tryout_answers agar urutan soal terkunci
            $hasCategoryCol = Schema::hasColumn('tryout_answers', 'category');
            $hasAssignedNoCol = Schema::hasColumn('tryout_answers', 'assigned_number');
            $questionsMap = DB::table('questions')->whereIn('id', $selectedQuestionIds)->get()->keyBy('id');

            foreach ($selectedQuestionIds as $idx => $qId) {
                $qObj = $questionsMap[$qId] ?? null;

                $ansInsert = [
                    'tryout_attempt_id'  => $attemptId,
                    'question_id'        => $qId,
                    'question_option_id' => null,
                    'score_earned'       => 0,
                    'time_spent_seconds' => 0,
                    'visit_count'        => 0,
                    'is_doubtful'        => 0,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];

                if ($hasCategoryCol) {
                    $ansInsert['category'] = $qObj->category ?? 'UMUM';
                }
                if ($hasAssignedNoCol) {
                    $ansInsert['assigned_number'] = $idx + 1;
                }

                DB::table('tryout_answers')->insert($ansInsert);
            }

            $attempt = DB::table('tryout_attempts')->where('id', $attemptId)->first();
        }

        // Hitung sisa waktu ujian dalam detik
        $startedAt = Carbon::parse($attempt->started_at);
        $endTime = $startedAt->copy()->addMinutes($tryout->duration_minutes ?: 30);
        $remainingSeconds = max(0, (int) $now->diffInSeconds($endTime, false));

        // Ambil daftar soal sesuai urutan di tryout_answers
        $answerRows = DB::table('tryout_answers')
            ->where('tryout_attempt_id', $attempt->id)
            ->orderBy('id', 'asc')
            ->get();

        $hasSubjCode = Schema::hasColumn('subjects', 'code');
        $questionsData = [];

        foreach ($answerRows as $index => $ans) {
            $qQuery = DB::table('questions')
                ->leftJoin('topics', 'questions.topic_id', '=', 'topics.id')
                ->leftJoin('subjects', 'topics.subject_id', '=', 'subjects.id')
                ->select(
                    'questions.*',
                    'topics.name as topic_name',
                    'subjects.name as subject_name'
                )
                ->where('questions.id', $ans->question_id);

            if ($hasSubjCode) {
                $qQuery->addSelect('subjects.code as subject_code');
            }

            $q = $qQuery->first();

            if ($q) {
                $options = DB::table('question_options')
                    ->where('question_id', $q->id)
                    ->orderBy('option_label', 'asc')
                    ->get()
                    ->map(function ($opt) {
                        return [
                            'id'    => $opt->id,
                            'label' => trim($opt->option_label),
                            'text'  => $opt->option_text,
                            'image' => $opt->option_image ?? null,
                        ];
                    });

                $categoryLabel = $q->category ?? ($q->subject_code ?? 'CAT');
                $chapterLabel  = $q->chapter ?? ($q->subject_name ?? 'Materi Ujian');
                $subChapLabel  = $q->sub_chapter ?? ($q->topic_name ?? 'Sub-Topik');

                $questionsData[] = [
                    'number'             => $index + 1,
                    'question_id'        => $q->id,
                    'category'           => $categoryLabel,
                    'chapter'            => $chapterLabel,
                    'sub_chapter'        => $subChapLabel,
                    'question_text'      => $q->question_text,
                    'question_image'     => $q->question_image ?? null,
                    'ideal_time_seconds' => $q->ideal_time_seconds ?? 54,
                    'selected_option_id' => $ans->question_option_id,
                    'selected_label'     => $this->getOptionLabel($options, $ans->question_option_id),
                    'is_doubtful'        => (bool) ($ans->is_doubtful ?? false),
                    'time_spent_seconds' => (int) ($ans->time_spent_seconds ?? 0),
                    'options'            => $options,
                ];
            }
        }

        return response()->json([
            'status'            => 'success',
            'attempt_id'        => $attempt->id,
            'remaining_seconds' => $remainingSeconds,
            'duration_minutes'  => $tryout->duration_minutes ?: 30,
            'questions'         => $questionsData,
        ]);
    }

    private function getOptionLabel($options, $optionId)
    {
        if (!$optionId) return null;
        foreach ($options as $opt) {
            if ($opt['id'] == $optionId) return $opt['label'];
        }
        return null;
    }

    /**
     * 3. Simpan Jawaban Peserta & Akumulasi Waktu per Soal (time_spent_seconds)
     */
    public function saveAnswer(Request $request, $attemptId)
    {
        $questionId   = $request->input('question_id');
        $optionId     = $request->input('option_id');
        $deltaSeconds = max(0, (int) $request->input('delta_seconds', 0));
        $isDoubtful   = $request->boolean('is_doubtful', false);
        $actionType   = $request->input('action_type', 'save'); // 'save' atau 'skip'

        $existing = DB::table('tryout_answers')
            ->where('tryout_attempt_id', $attemptId)
            ->where('question_id', $questionId)
            ->first();

        if (!$existing) {
            return response()->json(['status' => 'error', 'message' => 'Data soal tidak ditemukan'], 404);
        }

        $finalOptionId = ($actionType === 'save') ? $optionId : $existing->question_option_id;
        $scoreWeight = 0;

        if ($finalOptionId) {
            $opt = DB::table('question_options')->where('id', $finalOptionId)->first();
            $scoreWeight = $opt ? (int) $opt->score_weight : 0;
        }

        DB::table('tryout_answers')
            ->where('id', $existing->id)
            ->update([
                'question_option_id' => $finalOptionId,
                'score_earned'       => $scoreWeight,
                'is_doubtful'        => $isDoubtful ? 1 : 0,
                'first_opened_at'    => $existing->first_opened_at ?? now(),
                'last_answered_at'   => $finalOptionId ? now() : $existing->last_answered_at,
                'time_spent_seconds' => DB::raw("time_spent_seconds + {$deltaSeconds}"),
                'visit_count'        => DB::raw("visit_count + 1"),
                'updated_at'         => now(),
            ]);

        $updated = DB::table('tryout_answers')->where('id', $existing->id)->first();

        return response()->json([
            'status'             => 'success',
            'time_spent_seconds' => (int) $updated->time_spent_seconds,
            'visit_count'        => (int) $updated->visit_count,
        ]);
    }

    /**
     * 4. Selesaikan Ujian, Hitung Skor Total, & Update Tabel competency_diagnostics
     */
    public function finishExam(Request $request, $attemptId)
    {
        $attempt = DB::table('tryout_attempts')->where('id', $attemptId)->firstOrFail();

        // Update tambahan waktu pada soal terakhir yang sedang dibuka saat klik Selesai
        if ($request->filled('last_question_id')) {
            $delta = max(0, (int) $request->input('last_delta_seconds', 0));
            DB::table('tryout_answers')
                ->where('tryout_attempt_id', $attemptId)
                ->where('question_id', $request->input('last_question_id'))
                ->update([
                    'time_spent_seconds' => DB::raw("time_spent_seconds + {$delta}"),
                    'visit_count'        => DB::raw("visit_count + 1"),
                    'updated_at'         => now(),
                ]);
        }

        // Hitung total skor seluruh jawaban pada sesi ini
        $totalScore = (int) DB::table('tryout_answers')
            ->where('tryout_attempt_id', $attemptId)
            ->sum('score_earned');

        // Cek kelulusan Passing Grade (jika ada tryout_category_rules)
        $isPassed = true;
        if (Schema::hasTable('tryout_category_rules')) {
            $rules = DB::table('tryout_category_rules')->where('tryout_id', $attempt->tryout_id)->get();
            if ($rules->count() > 0) {
                $totalPgRequired = $rules->sum('passing_grade');
                $isPassed = $totalScore >= $totalPgRequired;
            } else {
                $isPassed = $totalScore >= 65;
            }
        } else {
            $isPassed = $totalScore >= 65;
        }

        // Tandai sesi tryout selesai dengan mengisi finished_at & is_passed
        DB::table('tryout_attempts')
            ->where('id', $attemptId)
            ->update([
                'finished_at' => now(),
                'total_score' => $totalScore,
                'is_passed'   => $isPassed ? 1 : 0,
                'updated_at'  => now(),
            ]);

        // =====================================================================
        // UPDATE OTOMATIS TABEL `competency_diagnostics` PER TOPIK
        // =====================================================================
        // Rekap dari seluruh jawaban ujian peserta yang sudah selesai untuk pemetaan akurat
        $userTopicAnswers = DB::table('tryout_answers')
            ->join('tryout_attempts', 'tryout_answers.tryout_attempt_id', '=', 'tryout_attempts.id')
            ->join('questions', 'tryout_answers.question_id', '=', 'questions.id')
            ->leftJoin('topics', 'questions.topic_id', '=', 'topics.id')
            ->where('tryout_attempts.user_id', $attempt->user_id)
            ->whereNotNull('tryout_attempts.finished_at')
            ->whereNotNull('questions.topic_id')
            ->select(
                'questions.topic_id',
                'topics.name as topic_name',
                'tryout_answers.score_earned',
                'tryout_answers.time_spent_seconds',
                'questions.category as q_category',
                'questions.chapter as q_chapter',
                'questions.sub_chapter as q_sub_chapter'
            )
            ->get()
            ->groupBy('topic_id');

        $hasDiagCategory = Schema::hasColumn('competency_diagnostics', 'category');
        $hasDiagChapter  = Schema::hasColumn('competency_diagnostics', 'chapter');
        $hasDiagSubChap  = Schema::hasColumn('competency_diagnostics', 'sub_chapter');

        foreach ($userTopicAnswers as $topicId => $items) {
            $totalAnswered = $items->count();
            $totalCorrect  = $items->where('score_earned', '>=', 4)->count();
            $totalTime     = (int) $items->sum('time_spent_seconds');
            $avgTime       = $totalAnswered > 0 ? round($totalTime / $totalAnswered, 1) : 0;
            $accuracy      = $totalAnswered > 0 ? round(($totalCorrect / $totalAnswered) * 100, 1) : 0;

            $statusLabel = 'Lemah';
            if ($accuracy >= 75) {
                $statusLabel = 'Unggul';
            } elseif ($accuracy >= 50) {
                $statusLabel = 'Cukup';
            }

            $firstItem = $items->first();

            $diagPayload = [
                'total_answered'      => $totalAnswered,
                'total_correct'       => $totalCorrect,
                'accuracy_percentage' => $accuracy,
                'total_time_seconds'  => $totalTime,
                'avg_time_seconds'    => $avgTime,
                'status_label'        => $statusLabel,
                'updated_at'          => now(),
            ];

            if ($hasDiagCategory) {
                $diagPayload['category'] = $firstItem->q_category ?? 'UMUM';
            }
            if ($hasDiagChapter) {
                $diagPayload['chapter'] = $firstItem->q_chapter ?? 'Materi Ujian';
            }
            if ($hasDiagSubChap) {
                $diagPayload['sub_chapter'] = $firstItem->q_sub_chapter ?? ($firstItem->topic_name ?? 'Sub-Topik');
            }

            $existingDiag = DB::table('competency_diagnostics')
                ->where('user_id', $attempt->user_id)
                ->where('topic_id', $topicId)
                ->first();

            if ($existingDiag) {
                DB::table('competency_diagnostics')
                    ->where('id', $existingDiag->id)
                    ->update($diagPayload);
            } else {
                $diagPayload['user_id']    = $attempt->user_id;
                $diagPayload['topic_id']   = $topicId;
                $diagPayload['created_at'] = now();
                DB::table('competency_diagnostics')->insert($diagPayload);
            }
        }

        return response()->json([
            'status'       => 'success',
            'total_score'  => $totalScore,
            'is_passed'    => $isPassed,
            'redirect_url' => url('/dashboard'),
        ]);
    }
}