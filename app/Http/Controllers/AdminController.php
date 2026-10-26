<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    private function authorizeAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus Administrator.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();
        $admin = Auth::user();

        // 1. CMS Beranda
        $siteSettings    = DB::table('site_settings')->orderBy('group')->orderBy('sort_order')->get();
        $landingMenus    = DB::table('landing_menus')->orderBy('location')->orderBy('sort_order')->get();
        $landingFeatures = DB::table('landing_features')->orderBy('sort_order')->get();

        // 2. Kurikulum
        $programs = DB::table('programs')->orderBy('sort_order')->get();
        $subjects = DB::table('subjects')
            ->join('programs', 'subjects.program_id', '=', 'programs.id')
            ->select('subjects.*', 'programs.name as program_name')
            ->orderBy('subjects.program_id')
            ->get();
        $topics = DB::table('topics')
            ->join('subjects', 'topics.subject_id', '=', 'subjects.id')
            ->select('topics.*', 'subjects.name as subject_name')
            ->orderBy('topics.subject_id')
            ->get();

        // 3. Video Materi & Progres
        $materials = DB::table('materials')
            ->join('topics', 'materials.topic_id', '=', 'topics.id')
            ->leftJoin('users', 'materials.instructor_id', '=', 'users.id')
            ->select('materials.*', 'topics.name as topic_name', 'users.name as instructor_name')
            ->orderByDesc('materials.id')
            ->get();

        $materialProgress = DB::table('user_material_progress')
            ->join('users', 'user_material_progress.user_id', '=', 'users.id')
            ->join('materials', 'user_material_progress.material_id', '=', 'materials.id')
            ->select('user_material_progress.*', 'users.name as user_name', 'materials.title as material_title')
            ->orderByDesc('user_material_progress.id')
            ->get();

        // 4. Master Paket Tryout & Bank Soal (Fleksibel berapapun jumlah soalnya)
        $questionCounts = DB::table('questions')
            ->select('tryout_id', DB::raw('count(*) as total'))
            ->groupBy('tryout_id')
            ->pluck('total', 'tryout_id');

        $tryouts = DB::table('tryouts')
            ->leftJoin('programs', 'tryouts.program_id', '=', 'programs.id')
            ->select('tryouts.*', 'programs.name as program_name')
            ->orderBy('tryouts.id')
            ->get()
            ->map(function ($to) use ($questionCounts) {
                $to->actual_questions_count = $questionCounts[$to->id] ?? 0;
                return $to;
            });

        $selectedTryoutId = $request->query('tryout_id', $tryouts->first()->id ?? null);

        $questionsQuery = DB::table('questions')
            ->join('tryouts', 'questions.tryout_id', '=', 'tryouts.id')
            ->join('topics', 'questions.topic_id', '=', 'topics.id')
            ->select('questions.*', 'tryouts.title as tryout_title', 'tryouts.code as tryout_code', 'topics.name as topic_name')
            ->orderBy('questions.tryout_id')
            ->orderBy('questions.question_number');

        if ($selectedTryoutId && $selectedTryoutId !== 'all') {
            $questionsQuery->where('questions.tryout_id', $selectedTryoutId);
        }

        $questions = $questionsQuery->get();
        $optionsByQuestion = DB::table('question_options')->orderBy('option_label')->get()->groupBy('question_id');
        $questions->transform(function ($q) use ($optionsByQuestion) {
            $q->options = $optionsByQuestion->get($q->id, collect())->values();
            return $q;
        });

        // Hitung nomor urut soal berikutnya secara otomatis untuk Tryout terpilih
        $nextQuestionNumber = ($selectedTryoutId && $selectedTryoutId !== 'all')
            ? ((int) DB::table('questions')->where('tryout_id', $selectedTryoutId)->max('question_number') + 1)
            : 1;

        // 5. Paket Penjualan & Isi Bundling
        $allPackageItems = DB::table('package_items')->get()->groupBy('package_id');
        $tryoutMap   = $tryouts->keyBy('id');
        $materialMap = $materials->keyBy('id');
        $users       = DB::table('users')->orderByDesc('id')->get();
        $userMap     = $users->keyBy('id');

        $packages = DB::table('packages')->orderBy('sort_order')->get()->map(function ($pkg) use ($allPackageItems, $tryoutMap, $materialMap, $userMap) {
            $pkg->features_array = json_decode($pkg->features ?? '[]', true) ?: [];
            $pkg->features_text  = implode("\n", $pkg->features_array);

            $items = $allPackageItems->get($pkg->id, collect());
            $pkg->tryout_ids   = $items->where('item_type', 'tryout')->pluck('item_id')->values()->toArray();
            $pkg->material_ids = $items->where('item_type', 'material')->pluck('item_id')->values()->toArray();
            $pkg->mentor_ids   = $items->where('item_type', 'private_1on1')->pluck('item_id')->values()->toArray();

            $pkg->bundled_tryouts   = collect($pkg->tryout_ids)->map(fn($id) => $tryoutMap[$id]->title ?? "Tryout #{$id}")->toArray();
            $pkg->bundled_materials = collect($pkg->material_ids)->map(fn($id) => $materialMap[$id]->title ?? "Materi #{$id}")->toArray();
            $pkg->bundled_mentors   = collect($pkg->mentor_ids)->map(fn($id) => $userMap[$id]->name ?? "Mentor #{$id}")->toArray();

            return $pkg;
        });

        // 6. Transaksi, Hak Akses & Evaluasi
        $transactions = DB::table('transactions')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->join('packages', 'transactions.package_id', '=', 'packages.id')
            ->select('transactions.*', 'users.name as user_name', 'users.email as user_email', 'packages.name as package_name')
            ->orderByDesc('transactions.id')
            ->get();

        $userAccesses = DB::table('user_accesses')
            ->join('users', 'user_accesses.user_id', '=', 'users.id')
            ->join('packages', 'user_accesses.package_id', '=', 'packages.id')
            ->select('user_accesses.*', 'users.name as user_name', 'packages.name as package_name')
            ->orderByDesc('user_accesses.id')
            ->get();

        $attempts = DB::table('tryout_attempts')
            ->join('users', 'tryout_attempts.user_id', '=', 'users.id')
            ->join('tryouts', 'tryout_attempts.tryout_id', '=', 'tryouts.id')
            ->select('tryout_attempts.*', 'users.name as user_name', 'tryouts.title as tryout_title')
            ->orderByDesc('tryout_attempts.id')
            ->get();

        $tryoutAnswers = DB::table('tryout_answers')
            ->join('tryout_attempts', 'tryout_answers.tryout_attempt_id', '=', 'tryout_attempts.id')
            ->join('users', 'tryout_attempts.user_id', '=', 'users.id')
            ->join('questions', 'tryout_answers.question_id', '=', 'questions.id')
            ->leftJoin('question_options', 'tryout_answers.question_option_id', '=', 'question_options.id')
            ->select(
                'tryout_answers.*',
                'users.name as user_name',
                'questions.question_number',
                'questions.question_text',
                'questions.ideal_time_seconds',
                'question_options.option_label'
            )
            ->orderByDesc('tryout_answers.id')
            ->limit(60)
            ->get();

        $diagnostics = DB::table('competency_diagnostics')
            ->join('users', 'competency_diagnostics.user_id', '=', 'users.id')
            ->join('topics', 'competency_diagnostics.topic_id', '=', 'topics.id')
            ->select('competency_diagnostics.*', 'users.name as user_name', 'topics.name as topic_name')
            ->orderByDesc('competency_diagnostics.id')
            ->get();

        $summary = [
            'users'          => $users->count(),
            'packages'       => $packages->count(),
            'tryouts'        => $tryouts->count(),
            'totalQuestions' => DB::table('questions')->count(),
            'revenue'        => $transactions->where('payment_status', 'paid')->sum('total_amount'),
            'paidTrx'        => $transactions->where('payment_status', 'paid')->count(),
        ];

        return view('admin.index', compact(
            'admin',
            'summary',
            'selectedTryoutId',
            'nextQuestionNumber',
            'siteSettings',
            'landingMenus',
            'landingFeatures',
            'programs',
            'subjects',
            'topics',
            'materials',
            'materialProgress',
            'tryouts',
            'questions',
            'packages',
            'transactions',
            'userAccesses',
            'users',
            'attempts',
            'tryoutAnswers',
            'diagnostics'
        ));
    }

    public function toggleBoolean(Request $request, $table, $id, $column)
    {
        $this->authorizeAdmin();
        $allowedTables = ['site_settings', 'landing_menus', 'landing_features', 'programs', 'subjects', 'materials', 'packages', 'tryouts', 'tryout_attempts'];
        $allowedCols   = ['is_displayed', 'show_on_homepage', 'is_featured', 'is_free_preview', 'is_active', 'is_passed'];

        if (in_array($table, $allowedTables) && in_array($column, $allowedCols)) {
            $rec = DB::table($table)->where('id', $id)->first();
            if ($rec) {
                DB::table($table)->where('id', $id)->update([
                    $column      => !$rec->$column,
                    'updated_at' => now(),
                ]);
            }
        }
        return back()->with('success', "Status pada tabel [{$table}] berhasil diperbarui!")
            ->with('active_tab', $request->input('active_tab', 'tab-overview'));
    }

    // 1. CMS Beranda
    public function updateSiteSettingsBulk(Request $request)
    {
        $this->authorizeAdmin();
        $values   = $request->input('settings', []);
        $displays = $request->input('displayed', []);
        foreach ($values as $id => $val) {
            DB::table('site_settings')->where('id', $id)->update([
                'value'        => $val,
                'is_displayed' => isset($displays[$id]) ? 1 : 0,
                'updated_at'   => now(),
            ]);
        }
        return back()->with('success', 'Pengaturan Beranda & Footer berhasil disimpan!')->with('active_tab', 'tab-cms');
    }

    public function storeSiteSetting(Request $request)
    {
        $this->authorizeAdmin();
        $data = [
            'key'          => Str::snake($request->key),
            'label'        => $request->label,
            'group'        => $request->group,
            'type'         => 'text',
            'value'        => $request->value,
            'is_displayed' => $request->has('is_displayed'),
            'sort_order'   => (int) $request->input('sort_order', 1),
            'updated_at'   => now(),
        ];
        if ($request->filled('id')) {
            DB::table('site_settings')->where('id', $request->id)->update($data);
        } else {
            $data['created_at'] = now();
            DB::table('site_settings')->insert($data);
        }
        return back()->with('success', 'Pengaturan Beranda berhasil disimpan!')->with('active_tab', 'tab-cms');
    }

    public function saveLandingMenu(Request $request)
    {
        $this->authorizeAdmin();
        $data = [
            'title'        => $request->title,
            'url'          => $request->url,
            'location'     => $request->location,
            'sort_order'   => (int) $request->input('sort_order', 1),
            'is_displayed' => $request->has('is_displayed'),
            'updated_at'   => now(),
        ];
        $request->filled('id')
            ? DB::table('landing_menus')->where('id', $request->id)->update($data)
            : DB::table('landing_menus')->insert(array_merge($data, ['created_at' => now()]));

        return back()->with('success', 'Menu navigasi berhasil disimpan!')->with('active_tab', 'tab-cms');
    }

    public function saveLandingFeature(Request $request)
    {
        $this->authorizeAdmin();
        $data = [
            'title'        => $request->title,
            'description'  => $request->description,
            'icon_key'     => $request->input('icon_key', 'chart'),
            'color_theme'  => $request->input('color_theme', 'blue'),
            'sort_order'   => (int) $request->input('sort_order', 1),
            'is_displayed' => $request->has('is_displayed'),
            'updated_at'   => now(),
        ];
        $request->filled('id')
            ? DB::table('landing_features')->where('id', $request->id)->update($data)
            : DB::table('landing_features')->insert(array_merge($data, ['created_at' => now()]));

        return back()->with('success', 'Kartu Fitur Unggulan berhasil disimpan!')->with('active_tab', 'tab-cms');
    }

    // 2. Kurikulum
    public function saveProgram(Request $request)
    {
        $this->authorizeAdmin();
        $data = [
            'name'              => $request->name,
            'slug'              => Str::slug($request->name),
            'badge_text'        => $request->badge_text,
            'description'       => $request->description,
            'custom_stat_label' => $request->custom_stat_label,
            'sort_order'        => (int) $request->input('sort_order', 1),
            'show_on_homepage'  => $request->has('show_on_homepage'),
            'updated_at'        => now(),
        ];
        $request->filled('id')
            ? DB::table('programs')->where('id', $request->id)->update($data)
            : DB::table('programs')->insert(array_merge($data, ['created_at' => now()]));

        return back()->with('success', 'Program berhasil disimpan!')->with('active_tab', 'tab-curriculum');
    }

    public function saveSubject(Request $request)
    {
        $this->authorizeAdmin();
        $data = [
            'program_id'    => $request->program_id,
            'name'          => $request->name,
            'passing_grade' => (int) $request->passing_grade,
            'is_displayed'  => $request->has('is_displayed'),
            'updated_at'    => now(),
        ];
        $request->filled('id')
            ? DB::table('subjects')->where('id', $request->id)->update($data)
            : DB::table('subjects')->insert(array_merge($data, ['created_at' => now()]));

        return back()->with('success', 'Mata Uji berhasil disimpan!')->with('active_tab', 'tab-curriculum');
    }

    public function saveTopic(Request $request)
    {
        $this->authorizeAdmin();
        $data = [
            'subject_id'  => $request->subject_id,
            'name'        => $request->name,
            'description' => $request->description,
            'updated_at'  => now(),
        ];
        $request->filled('id')
            ? DB::table('topics')->where('id', $request->id)->update($data)
            : DB::table('topics')->insert(array_merge($data, ['created_at' => now()]));

        return back()->with('success', 'Sub-Topik berhasil disimpan!')->with('active_tab', 'tab-curriculum');
    }

    // 3. Materi Video
    public function saveMaterial(Request $request)
    {
        $this->authorizeAdmin();
        $data = [
            'topic_id'        => $request->topic_id,
            'instructor_id'   => $request->instructor_id ?: null,
            'title'           => $request->title,
            'type'            => $request->type,
            'content_url'     => $request->content_url,
            'duration'        => (int) $request->duration,
            'is_free_preview' => $request->has('is_free_preview'),
            'updated_at'      => now(),
        ];
        $request->filled('id')
            ? DB::table('materials')->where('id', $request->id)->update($data)
            : DB::table('materials')->insert(array_merge($data, ['created_at' => now()]));

        return back()->with('success', 'Materi Pembelajaran berhasil disimpan!')->with('active_tab', 'tab-materials');
    }

    // 4. Master Paket Tryout & Bank Soal (DENGAN GAMBAR SOAL, GAMBAR PEMBAHASAN & GAMBAR OPSI A-E)
    public function saveTryout(Request $request)
    {
        $this->authorizeAdmin();
        $data = [
            'program_id'       => $request->program_id ?: null,
            'code'             => strtoupper($request->code ?: 'TO-' . rand(100, 999)),
            'title'            => $request->title,
            'type'             => $request->type,
            'duration_minutes' => (int) $request->duration_minutes,
            'total_questions'  => (int) $request->input('total_questions', 20),
            'instruction'      => $request->instruction,
            'is_active'        => $request->has('is_active'),
            'updated_at'       => now(),
        ];
        $request->filled('id')
            ? DB::table('tryouts')->where('id', $request->id)->update($data)
            : DB::table('tryouts')->insert(array_merge($data, ['created_at' => now()]));

        return back()->with('success', 'Master Paket Tryout berhasil disimpan!')->with('active_tab', 'tab-tryouts');
    }

    public function saveQuestionWithOptions(Request $request)
    {
        $this->authorizeAdmin();
        $qData = [
            'tryout_id'            => $request->tryout_id,
            'topic_id'             => $request->topic_id,
            'question_number'      => (int) $request->input('question_number', 1),
            'question_text'        => $request->question_text,
            'question_image'       => $request->question_image ?: null,
            'ideal_time_seconds'   => (int) $request->input('ideal_time_seconds', 54),
            'discussion_text'      => $request->discussion_text,
            'discussion_image'     => $request->discussion_image ?: null,
            'discussion_video_url' => $request->discussion_video_url ?: null,
            'updated_at'           => now(),
        ];

        if ($request->filled('id')) {
            $questionId = $request->id;
            DB::table('questions')->where('id', $questionId)->update($qData);
        } else {
            $qData['created_at'] = now();
            $questionId = DB::table('questions')->insertGetId($qData);
        }

        // Simpan 5 Pilihan Jawaban (A - E) lengkap dengan Teks Opsi, Link Gambar Opsi (`option_image`), & Bobot Poin
        foreach (['A', 'B', 'C', 'D', 'E'] as $lbl) {
            $optText   = $request->input("option_text_{$lbl}");
            $optImage  = $request->input("option_image_{$lbl}") ?: null;
            $optWeight = (int) $request->input("score_weight_{$lbl}", 0);

            // Opsi disimpan jika memiliki Teks ATAU Link Gambar
            if (($optText !== null && trim($optText) !== '') || $optImage !== null) {
                $existing = DB::table('question_options')
                    ->where('question_id', $questionId)
                    ->where('option_label', $lbl)
                    ->first();

                $payload = [
                    'option_text'  => $optText ?? ('Gambar Opsi ' . $lbl),
                    'option_image' => $optImage,
                    'score_weight' => $optWeight,
                    'updated_at'   => now(),
                ];

                if ($existing) {
                    DB::table('question_options')->where('id', $existing->id)->update($payload);
                } else {
                    DB::table('question_options')->insert(array_merge($payload, [
                        'question_id'  => $questionId,
                        'option_label' => $lbl,
                        'created_at'   => now(),
                    ]));
                }
            }
        }

        // Jika jumlah soal aktual melebihi target awal (misal lebih dari 20 soal), otomatis perbarui total_questions di tabel tryouts
        $actualCount = DB::table('questions')->where('tryout_id', $request->tryout_id)->count();
        $tryout = DB::table('tryouts')->where('id', $request->tryout_id)->first();
        if ($tryout && $actualCount > $tryout->total_questions) {
            DB::table('tryouts')->where('id', $request->tryout_id)->update(['total_questions' => $actualCount]);
        }

        return redirect()->route('admin.dashboard', ['tryout_id' => $request->tryout_id])
            ->with('success', "Soal No. {$qData['question_number']} beserta Opsi Jawaban (Teks/Gambar) berhasil disimpan!")
            ->with('active_tab', 'tab-questions');
    }

    // 5. Paket Penjualan & Isi Bundling
    public function savePackage(Request $request)
    {
        $this->authorizeAdmin();
        $featuresLines = array_values(array_filter(array_map('trim', explode("\n", $request->input('features_text', '')))));

        $data = [
            'name'                   => $request->name,
            'slug'                   => Str::slug($request->name) . '-' . rand(10, 99),
            'type'                   => $request->type,
            'badge_text'             => $request->badge_text,
            'short_description'      => $request->short_description,
            'features'               => json_encode($featuresLines),
            'private_sessions_count' => (int) $request->input('private_sessions_count', 0),
            'price'                  => (int) $request->price,
            'discount_price'         => $request->filled('discount_price') ? (int) $request->discount_price : null,
            'active_period_days'     => (int) $request->input('active_period_days', 365),
            'sort_order'             => (int) $request->input('sort_order', 1),
            'is_featured'            => $request->has('is_featured'),
            'show_on_homepage'       => $request->has('show_on_homepage'),
            'updated_at'             => now(),
        ];

        if ($request->filled('id')) {
            $packageId = $request->id;
            unset($data['slug']);
            DB::table('packages')->where('id', $packageId)->update($data);
            DB::table('package_items')->where('package_id', $packageId)->delete();
        } else {
            $data['created_at'] = now();
            $packageId = DB::table('packages')->insertGetId($data);
        }

        $now = now();
        $itemsToInsert = [];
        foreach ($request->input('tryout_ids', []) as $tid) {
            $itemsToInsert[] = ['package_id' => $packageId, 'item_type' => 'tryout', 'item_id' => (int) $tid, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now];
        }
        foreach ($request->input('material_ids', []) as $mid) {
            $itemsToInsert[] = ['package_id' => $packageId, 'item_type' => 'material', 'item_id' => (int) $mid, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now];
        }
        foreach ($request->input('mentor_ids', []) as $uid) {
            $itemsToInsert[] = [
                'package_id'  => $packageId,
                'item_type'   => 'private_1on1',
                'item_id'     => (int) $uid,
                'custom_note' => $data['private_sessions_count'] . 'x Sesi Mentoring Private 1-on-1',
                'created_at'  => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($itemsToInsert)) {
            DB::table('package_items')->insert($itemsToInsert);
        }

        return back()->with('success', 'Paket Belajar & Isi Bundling berhasil disimpan!')->with('active_tab', 'tab-packages');
    }

    // 6. Users, Transactions, Accesses & Diagnostics
    public function saveUser(Request $request)
    {
        $this->authorizeAdmin();
        $data = [
            'name'       => $request->name,
            'email'      => $request->email,
            'role'       => $request->role,
            'updated_at' => now(),
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        if ($request->filled('id')) {
            DB::table('users')->where('id', $request->id)->update($data);
        } else {
            $data['password']   = Hash::make($request->input('password', 'password123'));
            $data['created_at'] = now();
            DB::table('users')->insert($data);
        }
        return back()->with('success', 'Data Pengguna berhasil disimpan!')->with('active_tab', 'tab-users');
    }

    public function saveTransaction(Request $request)
    {
        $this->authorizeAdmin();
        $data = [
            'user_id'        => $request->user_id,
            'package_id'     => $request->package_id,
            'invoice_code'   => $request->invoice_code ?: 'INV-FA-' . date('Ymd') . '-' . rand(100, 999),
            'total_amount'   => (int) $request->total_amount,
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method ?: 'QRIS',
            'paid_at'        => $request->payment_status === 'paid' ? now() : null,
            'updated_at'     => now(),
        ];
        $request->filled('id')
            ? DB::table('transactions')->where('id', $request->id)->update($data)
            : DB::table('transactions')->insert(array_merge($data, ['created_at' => now()]));

        if ($request->payment_status === 'paid') {
            $exists = DB::table('user_accesses')->where('user_id', $request->user_id)->where('package_id', $request->package_id)->exists();
            if (!$exists) {
                DB::table('user_accesses')->insert([
                    'user_id'    => $request->user_id,
                    'package_id' => $request->package_id,
                    'expires_at' => now()->addDays(365),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        return back()->with('success', 'Transaksi berhasil disimpan!')->with('active_tab', 'tab-transactions');
    }

    public function saveUserAccess(Request $request)
    {
        $this->authorizeAdmin();
        $data = [
            'user_id'    => $request->user_id,
            'package_id' => $request->package_id,
            'expires_at' => $request->expires_at,
            'updated_at' => now(),
        ];
        $request->filled('id')
            ? DB::table('user_accesses')->where('id', $request->id)->update($data)
            : DB::table('user_accesses')->insert(array_merge($data, ['created_at' => now()]));

        return back()->with('success', 'Hak akses paket berhasil disimpan!')->with('active_tab', 'tab-transactions');
    }

    public function saveDiagnostic(Request $request)
    {
        $this->authorizeAdmin();
        $ans = max(1, (int) $request->total_answered);
        $cor = (int) $request->total_correct;
        $data = [
            'user_id'             => $request->user_id,
            'topic_id'            => $request->topic_id,
            'total_answered'      => $ans,
            'total_correct'       => $cor,
            'accuracy_percentage' => round(($cor / $ans) * 100, 1),
            'total_time_seconds'  => (int) $request->total_time_seconds,
            'avg_time_seconds'    => round(((int) $request->total_time_seconds) / $ans, 1),
            'status_label'        => $request->status_label,
            'updated_at'          => now(),
        ];
        $request->filled('id')
            ? DB::table('competency_diagnostics')->where('id', $request->id)->update($data)
            : DB::table('competency_diagnostics')->insert(array_merge($data, ['created_at' => now()]));

        return back()->with('success', 'Data Diagnostik berhasil disimpan!')->with('active_tab', 'tab-results');
    }

    public function destroyRecord(Request $request, $table, $id)
    {
        $this->authorizeAdmin();
        $allowedTables = [
            'site_settings',
            'landing_menus',
            'landing_features',
            'programs',
            'subjects',
            'topics',
            'materials',
            'user_material_progress',
            'tryouts',
            'questions',
            'question_options',
            'packages',
            'package_items',
            'transactions',
            'user_accesses',
            'users',
            'tryout_attempts',
            'tryout_answers',
            'competency_diagnostics'
        ];
        if (in_array($table, $allowedTables)) {
            if ($table === 'users' && (int) $id === Auth::id()) {
                return back()->with('error', 'Tidak dapat menghapus akun Admin aktif.');
            }
            DB::table($table)->where('id', $id)->delete();
        }
        return back()->with('success', "Data pada [{$table}] berhasil dihapus!")
            ->with('active_tab', $request->input('active_tab', 'tab-overview'));
    }
}
