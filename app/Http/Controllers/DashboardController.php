<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // 1. Pengaturan Brand
        $settings = DB::table('site_settings')
            ->whereIn('key', ['brand_name', 'brand_subname', 'footer_whatsapp'])
            ->pluck('value', 'key');

        // 2. Paket Belajar yang Dimiliki User Saat Ini (Murni milik $user->id)
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

        // 3. Data Competency Diagnostic Murni Milik User Saat Ini (Tanpa Fallback Palsu!)
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

        // 4. Daftar Video Materi & Progres Tontonan User
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

        // Rekomendasi Video Berdasarkan Topik Lemah User
        $weakTopicIds = $diagnostics->where('status_label', 'Lemah')->pluck('topic_id')->toArray();
        $recommendedMaterials = $materials->whereIn('topic_id', $weakTopicIds)->values();

        // 5. Daftar Tryout & Riwayat Percobaan (Attempts) Murni Milik User
        $attemptsHistory = DB::table('tryout_attempts')
            ->join('tryouts', 'tryout_attempts.tryout_id', '=', 'tryouts.id')
            ->where('tryout_attempts.user_id', $user->id)
            ->select('tryout_attempts.*', 'tryouts.title as tryout_title', 'tryouts.type as tryout_type', 'tryouts.duration_minutes')
            ->orderByDesc('tryout_attempts.finished_at')
            ->get();

        $tryouts = DB::table('tryouts')->get()->map(function ($to) use ($attemptsHistory) {
            $to->user_attempts_count = $attemptsHistory->where('tryout_id', $to->id)->count();
            $to->best_score = $attemptsHistory->where('tryout_id', $to->id)->max('total_score');
            return $to;
        });

        // 6. Log Kecepatan Per Butir Soal Murni Milik User Saat Ini (Tanpa Fallback ke ID 3!)
        $questionSpeedLogs = DB::table('tryout_answers')
            ->join('tryout_attempts', 'tryout_answers.tryout_attempt_id', '=', 'tryout_attempts.id')
            ->join('questions', 'tryout_answers.question_id', '=', 'questions.id')
            ->join('topics', 'questions.topic_id', '=', 'topics.id')
            ->where('tryout_attempts.user_id', $user->id)
            ->select(
                'tryout_answers.*',
                'questions.question_text',
                'questions.ideal_time_seconds',
                'topics.name as topic_name'
            )
            ->orderByDesc('tryout_answers.id')
            ->get();

        // 7. Katalog Semua Paket Belajar & Transaksi User
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

        // 8. Ringkasan Tambahan Jika Login Sebagai ADMIN atau MENTOR (Instructor)
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
            'materials',
            'recommendedMaterials',
            'tryouts',
            'attemptsHistory',
            'questionSpeedLogs',
            'catalogPackages',
            'transactions',
            'roleStats'
        ));
    }

    // Fungsi Menyimpan / Mengubah Password (Untuk User Google maupun Reguler)
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
