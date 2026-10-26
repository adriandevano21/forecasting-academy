<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BerandaController extends Controller
{
    public function index()
    {
        // 1. Ambil seluruh pengaturan teks Beranda & Footer yang statusnya ditampilkan (is_displayed = true)
        $rawSettings = DB::table('site_settings')
            ->where('is_displayed', true)
            ->orderBy('sort_order')
            ->get();

        // Map key => value untuk pemanggilan cepat di Blade (jika tipe JSON otomatis di-decode)
        $settings = [];
        foreach ($rawSettings as $item) {
            $settings[$item->key] = $item->type === 'json'
                ? json_decode($item->value, true)
                : $item->value;
        }

        // Ambil grup kontak & sosial media khusus untuk perulangan di Footer
        $footerContacts = $rawSettings->where('group', 'footer_contact')->values();
        $footerSocials = $rawSettings->where('group', 'footer_social')->values();

        // 2. Ambil Menu Navigasi Atas & Tautan Cepat Footer yang aktif (is_displayed = true)
        $navbarMenus = DB::table('landing_menus')
            ->where('location', 'navbar')
            ->where('is_displayed', true)
            ->orderBy('sort_order')
            ->get();

        $footerQuicklinks = DB::table('landing_menus')
            ->where('location', 'footer_quicklink')
            ->where('is_displayed', true)
            ->orderBy('sort_order')
            ->get();

        // 3. Ambil Fitur Unggulan yang aktif (is_displayed = true)
        $features = DB::table('landing_features')
            ->where('is_displayed', true)
            ->orderBy('sort_order')
            ->get();

        // 4. Ambil Program Kurikulum yang diatur tampil di beranda (show_on_homepage = true)
        $programs = DB::table('programs')
            ->where('show_on_homepage', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($program) {
                $program->subjects = DB::table('subjects')
                    ->where('program_id', $program->id)
                    ->where('is_displayed', true)
                    ->get();
                return $program;
            });

        // 5. Ambil Paket Belajar yang diatur tampil di beranda (show_on_homepage = true)
        $packages = DB::table('packages')
            ->where('show_on_homepage', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($pkg) {
                $pkg->features_list = json_decode($pkg->features ?? '[]', true);
                return $pkg;
            });

        // 6. Ambil sampel Live Competency Diagnostic & Soal dari DB untuk preview interaktif di Hero
        $sampleQuestion = DB::table('questions')
            ->join('topics', 'questions.topic_id', '=', 'topics.id')
            ->select('questions.*', 'topics.name as topic_name')
            ->first();

        $sampleDiagnostics = DB::table('competency_diagnostics')
            ->join('topics', 'competency_diagnostics.topic_id', '=', 'topics.id')
            ->select('competency_diagnostics.*', 'topics.name as topic_name')
            ->orderByDesc('accuracy_percentage')
            ->get();

        // dd($programs);

        return view('beranda', compact(
            'settings',
            'footerContacts',
            'footerSocials',
            'navbarMenus',
            'footerQuicklinks',
            'features',
            'programs',
            'packages',
            'sampleQuestion',
            'sampleDiagnostics'
        ));
    }
}
