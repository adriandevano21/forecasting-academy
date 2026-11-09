<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Data bersama untuk Template Navigasi (Sidebar, Topbar, Bottom Nav)
     */
    private function getSharedLayoutData($user)
    {
        $settings = DB::table('site_settings')
            ->whereIn('key', ['brand_name', 'brand_subname', 'footer_whatsapp'])
            ->pluck('value', 'key');

        $diagnostics = DB::table('competency_diagnostics')
            ->join('topics', 'competency_diagnostics.topic_id', '=', 'topics.id')
            ->join('subjects', 'topics.subject_id', '=', 'subjects.id')
            ->where('competency_diagnostics.user_id', $user->id)
            ->select(
                'competency_diagnostics.*',
                'topics.name as topic_name',
                'topics.description as topic_desc',
                'subjects.name as subject_name'
            )
            ->orderBy('accuracy_percentage', 'asc')
            ->get();

        $diagSummary = [
            'unggul'       => $diagnostics->where('status_label', 'Unggul')->count(),
            'cukup'        => $diagnostics->where('status_label', 'Cukup')->count(),
            'lemah'        => $diagnostics->where('status_label', 'Lemah')->count(),
            'avg_accuracy' => $diagnostics->count() > 0 ? round($diagnostics->avg('accuracy_percentage'), 1) : 0,
            'avg_speed'    => $diagnostics->count() > 0 ? round($diagnostics->avg('avg_time_seconds'), 0) : 0,
        ];

        $tryoutsCount = DB::table('tryouts')->count();
        $attemptsCount = DB::table('tryout_attempts')->where('user_id', $user->id)->count();

        return compact('settings', 'diagnostics', 'diagSummary', 'tryoutsCount', 'attemptsCount');
    }

    /**
     * Halaman Inti Dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();

        $shared = $this->getSharedLayoutData($user);
        $settings = $shared['settings'];
        $diagnostics = $shared['diagnostics'];
        $diagSummary = $shared['diagSummary'];
        $tryoutsCount = $shared['tryoutsCount'];
        $attemptsCount = $shared['attemptsCount'];

        // Paket Belajar yang Dimiliki User
        $myAccesses = DB::table('user_accesses')
            ->join('packages', 'user_accesses.package_id', '=', 'packages.id')
            ->where('user_accesses.user_id', $user->id)
            ->select(
                'user_accesses.*',
                'packages.name as package_name',
                'packages.type as package_type',
                'packages.badge_text',
                'packages.features'
            )
            ->orderByDesc('user_accesses.expires_at')
            ->get()
            ->map(function ($acc) use ($now) {
                $expires = Carbon::parse($acc->expires_at);
                $acc->days_remaining = max(0, (int) $now->diffInDays($expires, false));
                $acc->is_active = $expires->isFuture();
                $acc->features_list = json_decode($acc->features ?? '[]', true);
                return $acc;
            });

        // Daftar Video Materi & Progres Tontonan User
        $materials = DB::table('materials')
            ->join('topics', 'materials.topic_id', '=', 'topics.id')
            ->join('subjects', 'topics.subject_id', '=', 'subjects.id')
            ->leftJoin('users as instructors', 'materials.instructor_id', '=', 'instructors.id')
            ->leftJoin('user_material_progress', function ($join) use ($user) {
                $join->on('materials.id', '=', 'user_material_progress.material_id')
                    ->where('user_material_progress.user_id', '=', $user->id);
            })
            ->select(
                'materials.*',
                'topics.name as topic_name',
                'subjects.name as subject_name',
                'instructors.name as instructor_name',
                'user_material_progress.is_completed',
                'user_material_progress.last_watched_second'
            )
            ->get()
            ->map(function ($mat) {
                $duration = max(1, (int) $mat->duration);
                $watched = (int) ($mat->last_watched_second ?? 0);
                $mat->progress_percent = $mat->is_completed ? 100 : min(99, round(($watched / $duration) * 100));
                $mat->duration_minutes = ceil($duration / 60);
                return $mat;
            });

        $weakTopicIds = $diagnostics->where('status_label', 'Lemah')->pluck('topic_id')->toArray();
        $recommendedMaterials = $materials->whereIn('topic_id', $weakTopicIds)->values();

        // Daftar Riwayat Sesi Tryout yang Sudah Dilakukan User
        $attemptsHistory = DB::table('tryout_attempts')
            ->join('tryouts', 'tryout_attempts.tryout_id', '=', 'tryouts.id')
            ->where('tryout_attempts.user_id', $user->id)
            ->select(
                'tryout_attempts.*',
                'tryouts.title as tryout_title',
                'tryouts.type as tryout_type',
                'tryouts.duration_minutes',
                'tryouts.total_questions'
            )
            ->orderByDesc('tryout_attempts.finished_at')
            ->orderByDesc('tryout_attempts.id')
            ->get()
            ->map(function ($att) {
                // Hitung statistik ringkas per attempt dari tryout_answers
                $ansStats = DB::table('tryout_answers')
                    ->where('tryout_attempt_id', $att->id)
                    ->selectRaw('COUNT(*) as answered_count, AVG(time_spent_seconds) as avg_speed')
                    ->first();

                $att->answered_count = $ansStats->answered_count ?? 0;
                $att->avg_speed = round($ansStats->avg_speed ?? 0, 0);
                return $att;
            });

        // Daftar Semua Simulasi Tryout
        $tryouts = DB::table('tryouts')->get()->map(function ($to) use ($attemptsHistory) {
            $userAttempts = $attemptsHistory->where('tryout_id', $to->id);
            $to->user_attempts_count = $userAttempts->count();
            $to->best_score = $userAttempts->max('total_score');
            $to->latest_attempt_id = $userAttempts->first()->id ?? null;
            return $to;
        });

        // Katalog Paket Belajar & Transaksi
        $catalogPackages = DB::table('packages')
            ->orderBy('sort_order')
            ->get()
            ->map(function ($pkg) use ($myAccesses) {
                $pkg->features_list = json_decode($pkg->features ?? '[]', true);
                $pkg->is_owned = $myAccesses->where('package_id', $pkg->id)->where('is_active', true)->isNotEmpty();
                return $pkg;
            });

        $transactions = DB::table('transactions')
            ->join('packages', 'transactions.package_id', '=', 'packages.id')
            ->where('transactions.user_id', $user->id)
            ->select('transactions.*', 'packages.name as package_name')
            ->orderByDesc('transactions.created_at')
            ->get();

        // Statistik Role Admin / Mentor
        $roleStats = null;
        if ($user->role === 'admin') {
            $roleStats = [
                'total_students' => DB::table('users')->where('role', 'student')->count(),
                'total_mentors'  => DB::table('users')->where('role', 'instructor')->count(),
                'total_revenue'  => DB::table('transactions')->where('payment_status', 'paid')->sum('total_amount'),
                'total_attempts' => DB::table('tryout_attempts')->count(),
            ];
        } elseif ($user->role === 'instructor') {
            $roleStats = [
                'my_materials_count' => DB::table('materials')->where('instructor_id', $user->id)->count(),
                'total_views'        => DB::table('user_material_progress')
                    ->join('materials', 'user_material_progress.material_id', '=', 'materials.id')
                    ->where('materials.instructor_id', $user->id)
                    ->count(),
            ];
        }

        return view('dashboard.index', compact(
            'user',
            'settings',
            'myAccesses',
            'diagnostics',
            'diagSummary',
            'tryoutsCount',
            'attemptsCount',
            'materials',
            'recommendedMaterials',
            'tryouts',
            'attemptsHistory',
            'catalogPackages',
            'transactions',
            'roleStats'
        ));
    }

    /**
     * Halaman Khusus Analisis & Hasil Per Sesi Tryout
     */
    public function analysis($attemptId)
    {
        // Ambil objek $user yang sedang login (atau fallback ke user student ID 3)
        $user = Auth::user() ?? DB::table('users')->where('id', 3)->first();
        $userId = $user->id;

        // Ambil pengaturan brand & ringkasan sidebar untuk layout dashboard.layouts.app
        $settings = DB::table('site_settings')
            ->whereIn('key', ['brand_name', 'brand_subname', 'footer_whatsapp'])
            ->pluck('value', 'key');

        $tryouts = DB::table('tryouts')->get();

        // 1. Ambil Data Attempt & Master Tryout
        $attempt = DB::table('tryout_attempts as ta')
            ->join('tryouts as t', 't.id', '=', 'ta.tryout_id')
            ->select(
                'ta.*',
                't.title as tryout_title',
                't.code as tryout_code',
                't.type as tryout_type',
                't.duration_minutes',
                't.total_questions'
            )
            ->where('ta.id', $attemptId)
            ->where('ta.user_id', $userId)
            ->first();

        if (!$attempt) {
            abort(404, 'Data sesi tryout tidak ditemukan.');
        }

        // 2. Daftar Semua Sesi Tryout milik User (untuk Dropdown Switcher)
        $allAttempts = DB::table('tryout_attempts as ta')
            ->join('tryouts as t', 't.id', '=', 'ta.tryout_id')
            ->select('ta.id', 'ta.total_score', 'ta.finished_at', 'ta.is_passed', 't.title as tryout_title')
            ->where('ta.user_id', $userId)
            ->orderByDesc('ta.id')
            ->get();

        // 3. Ambil Detail Jawaban Peserta + Soal + Topik + Mata Uji
        $rawAnswers = DB::table('tryout_answers as ans')
            ->join('questions as q', 'q.id', '=', 'ans.question_id')
            ->leftJoin('topics as top', 'top.id', '=', 'q.topic_id')
            ->leftJoin('subjects as sub', 'sub.id', '=', 'top.subject_id')
            ->select(
                'ans.id as answer_id',
                'ans.question_id',
                'ans.question_option_id as selected_option_id',
                'ans.category as ans_category',
                'ans.assigned_number',
                'ans.score_earned',
                'ans.time_spent_seconds',
                'ans.visit_count',
                'ans.is_doubtful',
                'q.question_number',
                'q.category as q_category',
                'q.chapter',
                'q.sub_chapter',
                'q.question_text',
                'q.question_image',
                'q.ideal_time_seconds',
                'q.discussion_text',
                'q.discussion_image',
                'q.discussion_video_url',
                'q.topic_id',
                'top.name as topic_name',
                'top.description as topic_desc',
                'sub.name as subject_name',
                'sub.code as subject_code'
            )
            ->where('ans.tryout_attempt_id', $attempt->id)
            ->orderByRaw('COALESCE(ans.assigned_number, q.question_number, ans.id) ASC')
            ->get();

        if ($rawAnswers->isEmpty()) {
            $rawAnswers = DB::table('questions as q')
                ->leftJoin('topics as top', 'top.id', '=', 'q.topic_id')
                ->leftJoin('subjects as sub', 'sub.id', '=', 'top.subject_id')
                ->select(
                    DB::raw('NULL as answer_id'),
                    'q.id as question_id',
                    DB::raw('NULL as selected_option_id'),
                    'q.category as ans_category',
                    'q.question_number as assigned_number',
                    DB::raw('0 as score_earned'),
                    DB::raw('0 as time_spent_seconds'),
                    DB::raw('0 as visit_count'),
                    DB::raw('0 as is_doubtful'),
                    'q.question_number',
                    'q.category as q_category',
                    'q.chapter',
                    'q.sub_chapter',
                    'q.question_text',
                    'q.question_image',
                    'q.ideal_time_seconds',
                    'q.discussion_text',
                    'q.discussion_image',
                    'q.discussion_video_url',
                    'q.topic_id',
                    'top.name as topic_name',
                    'top.description as topic_desc',
                    'sub.name as subject_name',
                    'sub.code as subject_code'
                )
                ->where('q.tryout_id', $attempt->tryout_id)
                ->orderBy('q.question_number')
                ->get();
        }

        // 4. Ambil Semua Opsi Jawaban (A-E)
        $questionIds = $rawAnswers->pluck('question_id')->unique()->filter()->values();
        $optionsByQuestion = collect();

        if ($questionIds->isNotEmpty()) {
            $optionsByQuestion = DB::table('question_options')
                ->whereIn('question_id', $questionIds)
                ->orderBy('option_label')
                ->get()
                ->groupBy('question_id');
        }

        // 5. Susun Objek Lengkap Per Butir Soal ($questionLogs)
        $questionLogs = $rawAnswers->values()->map(function ($row, $idx) use ($optionsByQuestion) {
            $options = $optionsByQuestion->get($row->question_id, collect());
            $maxWeight = (int) ($options->max('score_weight') ?? 5);
            $correctOption = $options->sortByDesc('score_weight')->first();
            $selectedOption = $options->firstWhere('id', $row->selected_option_id);

            $category = $row->ans_category ?: ($row->q_category ?: ($row->subject_code ?: 'TIU'));
            $idealSeconds = (int) ($row->ideal_time_seconds ?: 54);
            $spentSeconds = (int) ($row->time_spent_seconds ?: 0);
            $scoreEarned = (int) ($row->score_earned ?: 0);

            $isAnswered = !is_null($row->selected_option_id);
            $isCorrect = $isAnswered && ($scoreEarned >= $maxWeight && $maxWeight > 0);
            $isPartial = $isAnswered && !$isCorrect && ($scoreEarned > 0);
            $isOvertime = $spentSeconds > $idealSeconds;

            $row->display_number = $row->assigned_number ?: ($row->question_number ?: ($idx + 1));
            $row->category = strtoupper($category);
            $row->chapter = $row->chapter ?: ($row->subject_name ?: 'Kompetensi Utama');
            $row->sub_chapter = $row->sub_chapter ?: ($row->topic_name ?: 'Sub-Topik Umum');
            $row->topic_name = $row->topic_name ?: $row->sub_chapter;
            $row->ideal_time_seconds = $idealSeconds;
            $row->time_spent_seconds = $spentSeconds;
            $row->visit_count = max(1, (int) $row->visit_count);
            $row->is_doubtful = (bool) $row->is_doubtful;
            $row->is_overtime = $isOvertime;
            $row->is_answered = $isAnswered;
            $row->is_answer_correct = $isCorrect;
            $row->is_partial = $isPartial;
            $row->max_weight = $maxWeight;

            if (!$isAnswered) {
                $row->answer_status = 'empty';
            } elseif ($isCorrect) {
                $row->answer_status = 'correct';
            } elseif ($isPartial) {
                $row->answer_status = 'partial';
            } else {
                $row->answer_status = 'wrong';
            }

            $row->options = $options;
            $row->selected_option = $selectedOption;
            $row->correct_option = $correctOption;

            return $row;
        });

        // 6. Hitung Rincian Skor Per Kategori & Passing Grade
        $categoryRules = collect();
        if (DB::getSchemaBuilder()->hasTable('tryout_category_rules')) {
            $categoryRules = DB::table('tryout_category_rules')
                ->where('tryout_id', $attempt->tryout_id)
                ->orderBy('sort_order')
                ->get()
                ->keyBy(fn ($r) => strtoupper($r->category));
        }

        $categoryBreakdown = $questionLogs->groupBy('category')->map(function ($items, $cat) use ($categoryRules) {
            $rule = $categoryRules->get(strtoupper($cat));
            $passingGrade = $rule ? (int) $rule->passing_grade : ($cat === 'TWK' ? 65 : ($cat === 'TIU' ? 80 : ($cat === 'TKP' ? 166 : 65)));
            $earnedScore = (int) $items->sum('score_earned');
            $maxPossibleScore = (int) $items->sum('max_weight');
            $correctCount = $items->where('is_answer_correct', true)->count();
            $totalCount = $items->count();
            $avgTime = $totalCount > 0 ? round($items->avg('time_spent_seconds')) : 0;

            return (object) [
                'category' => $cat,
                'score_earned' => $earnedScore,
                'max_score' => $maxPossibleScore > 0 ? $maxPossibleScore : ($totalCount * 5),
                'passing_grade' => $passingGrade,
                'is_passed' => $earnedScore >= $passingGrade,
                'correct_count' => $correctCount,
                'total_questions' => $totalCount,
                'avg_time' => $avgTime,
                'percentage' => $maxPossibleScore > 0 ? min(100, round(($earnedScore / $maxPossibleScore) * 100)) : 0,
            ];
        })->values();

        // 7. Rekapitulasi Peta Kompetensi Per Topik / Sub-Bab pada Sesi Ini
        $attemptTopics = $questionLogs->groupBy('topic_id')->map(function ($items) {
            $first = $items->first();
            $totalAnswered = $items->count();
            $totalCorrect = $items->where('is_answer_correct', true)->count();
            $accuracy = $totalAnswered > 0 ? round(($totalCorrect / $totalAnswered) * 100, 1) : 0;
            $avgTime = $totalAnswered > 0 ? round($items->avg('time_spent_seconds'), 0) : 0;

            if ($accuracy >= 75) {
                $statusLabel = 'Unggul';
            } elseif ($accuracy >= 50) {
                $statusLabel = 'Cukup';
            } else {
                $statusLabel = 'Lemah';
            }

            return (object) [
                'topic_id' => $first->topic_id,
                'category' => $first->category,
                'subject_name' => $first->subject_name ?: $first->category,
                'chapter' => $first->chapter,
                'topic_name' => $first->sub_chapter ?: $first->topic_name,
                'topic_desc' => $first->topic_desc ?: ('Evaluasi sub-bab ' . ($first->sub_chapter ?: $first->topic_name)),
                'total_answered' => $totalAnswered,
                'total_correct' => $totalCorrect,
                'accuracy_percentage' => $accuracy,
                'avg_time_seconds' => $avgTime,
                'status_label' => $statusLabel,
            ];
        })->values();

        // 8. Ringkasan Metrik Utama ($attemptMetrics) & $diagSummary untuk Sidebar/Topbar
        $totalQuestionsCount = $questionLogs->count();
        $totalAnsweredCount = $questionLogs->where('is_answered', true)->count();
        $totalCorrectCount = $questionLogs->where('is_answer_correct', true)->count();
        $totalPartialCount = $questionLogs->where('is_partial', true)->count();
        $totalWrongCount = $questionLogs->where('answer_status', 'wrong')->count();
        $totalEmptyCount = $questionLogs->where('answer_status', 'empty')->count();
        $totalTimeSpent = (int) $questionLogs->sum('time_spent_seconds');

        $attemptMetrics = [
            'total_questions' => $totalQuestionsCount,
            'total_answered' => $totalAnsweredCount,
            'total_correct' => $totalCorrectCount,
            'total_partial' => $totalPartialCount,
            'total_wrong' => $totalWrongCount,
            'total_empty' => $totalEmptyCount,
            'accuracy' => $totalQuestionsCount > 0 ? round(($totalCorrectCount / $totalQuestionsCount) * 100) : 0,
            'avg_speed' => $totalQuestionsCount > 0 ? round($questionLogs->avg('time_spent_seconds')) : 0,
            'avg_ideal' => $totalQuestionsCount > 0 ? round($questionLogs->avg('ideal_time_seconds')) : 54,
            'total_time_minutes' => round($totalTimeSpent / 60, 1),
            'overtime_count' => $questionLogs->where('is_overtime', true)->count(),
            'doubtful_count' => $questionLogs->where('is_doubtful', true)->count(),
            'unggul_count' => $attemptTopics->where('status_label', 'Unggul')->count(),
            'cukup_count' => $attemptTopics->where('status_label', 'Cukup')->count(),
            'lemah_count' => $attemptTopics->where('status_label', 'Lemah')->count(),
        ];

        $diagSummary = [
            'unggul' => $attemptMetrics['unggul_count'],
            'cukup' => $attemptMetrics['cukup_count'],
            'lemah' => $attemptMetrics['lemah_count'],
            'avg_accuracy' => $attemptMetrics['accuracy'],
            'avg_speed' => $attemptMetrics['avg_speed'],
        ];

        // 9. Rekomendasi Materi Video
        $weakTopicIds = $attemptTopics->whereIn('status_label', ['Lemah', 'Cukup'])->pluck('topic_id')->filter();
        $recommendedMaterialsQuery = DB::table('materials as m')
            ->join('topics as top', 'top.id', '=', 'm.topic_id')
            ->join('subjects as sub', 'sub.id', '=', 'top.subject_id')
            ->leftJoin('users as u', 'u.id', '=', 'm.instructor_id')
            ->select(
                'm.*',
                'top.name as topic_name',
                'sub.name as subject_name',
                'u.name as instructor_name'
            );

        if ($weakTopicIds->isNotEmpty()) {
            $recommendedMaterialsQuery->whereIn('m.topic_id', $weakTopicIds);
        }

        $recommendedMaterials = $recommendedMaterialsQuery->limit(4)->get();

        return view('dashboard.analysis', compact(
            'user',
            'settings',
            'tryouts',
            'diagSummary',
            'attempt',
            'allAttempts',
            'attemptMetrics',
            'categoryBreakdown',
            'attemptTopics',
            'questionLogs',
            'recommendedMaterials'
        ));
    }

    /**
     * Simpan / Perbarui Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'new_password.min' => 'Password minimal harus terdiri dari 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();
        DB::table('users')->where('id', $user->id)->update([
            'password' => Hash::make($request->new_password),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Password akun Anda berhasil disimpan! Sekarang Anda dapat login menggunakan Email & Password.');
    }
}