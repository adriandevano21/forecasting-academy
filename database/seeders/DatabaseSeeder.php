<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $defaultPassword = Hash::make('password123');

        // =====================================================================
        // 1. SEED USERS (ADMIN, 2 MENTOR, & 3 PESERTA)
        // =====================================================================
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Admin Utama Forecasting',
            'email' => 'admin@forecasting.test',
            'password' => $defaultPassword,
            'role' => 'admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $mentorStisId = DB::table('users')->insertGetId([
            'name' => 'Budi Santoso, M.Stat (Mentor STIS & TIU)',
            'email' => 'budi.mentor@gmail.com',
            'google_id' => '10928374651029384756',
            'password' => $defaultPassword,
            'role' => 'instructor',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $mentorPsiId = DB::table('users')->insertGetId([
            'name' => 'Siti Rahma, M.Psi (Mentor Psikotes & TKP)',
            'email' => 'siti.psikolog@gmail.com',
            'google_id' => '10928374651029389999',
            'password' => $defaultPassword,
            'role' => 'instructor',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $studentAndiId = DB::table('users')->insertGetId([
            'name' => 'Andi Pratama',
            'email' => 'andi.student@gmail.com',
            'google_id' => '98765432101234567890',
            'password' => $defaultPassword,
            'role' => 'student',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $studentCitraId = DB::table('users')->insertGetId([
            'name' => 'Citra Lestari',
            'email' => 'citra.peserta@gmail.com',
            'password' => $defaultPassword,
            'role' => 'student',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $studentDimasId = DB::table('users')->insertGetId([
            'name' => 'Dimas Anggara',
            'email' => 'dimas.baru@gmail.com',
            'password' => $defaultPassword,
            'role' => 'student',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // =====================================================================
        // 2. SEED SITE SETTINGS, MENUS, & FEATURES (UNTUK BERANDA)
        // =====================================================================
        DB::table('site_settings')->insert([
            ['key' => 'brand_name', 'group' => 'general', 'label' => 'Nama Brand Utama', 'type' => 'text', 'value' => 'Forecasting', 'is_displayed' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'brand_subname', 'group' => 'general', 'label' => 'Sub Nama Brand', 'type' => 'text', 'value' => 'Academy', 'is_displayed' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'hero_badge', 'group' => 'hero', 'label' => 'Badge Atas Hero', 'type' => 'text', 'value' => 'Platform E-Learning & Smart CBT #1 Persiapan Kedinasan', 'is_displayed' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'hero_title_prefix', 'group' => 'hero', 'label' => 'Judul Hero Awal', 'type' => 'text', 'value' => 'Petakan Kompetensimu, Taklukkan', 'is_displayed' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'hero_title_highlight', 'group' => 'hero', 'label' => 'Judul Hero Highlight', 'type' => 'text', 'value' => 'SKD, STIS & Psikotes', 'is_displayed' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'hero_title_suffix', 'group' => 'hero', 'label' => 'Judul Hero Akhir', 'type' => 'text', 'value' => 'Lebih Cepat!', 'is_displayed' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'hero_description', 'group' => 'hero', 'label' => 'Deskripsi Hero', 'type' => 'text', 'value' => 'Forecasting Academy menggabungkan Video Pembelajaran, Simulasi CBT dengan Pengacakan Berurutan per Kategori (TWK, TIU, TKP), serta teknologi Competency Diagnostic & Time-Spent Analytics.', 'is_displayed' => true, 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'hero_primary_btn', 'group' => 'hero', 'label' => 'Teks Tombol Utama Hero', 'type' => 'text', 'value' => 'Lihat Paket Belajar', 'is_displayed' => true, 'sort_order' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'hero_secondary_btn', 'group' => 'hero', 'label' => 'Teks Tombol Kedua Hero', 'type' => 'text', 'value' => 'Pelajari Cara Kerja', 'is_displayed' => true, 'sort_order' => 7, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'hero_stats', 'group' => 'hero', 'label' => 'Statistik Hero', 'type' => 'json', 'value' => json_encode([
                ['number' => '54', 'unit' => 'dtk', 'label' => 'Pelacakan Waktu Ideal/Soal'],
                ['number' => '3', 'unit' => 'Level', 'label' => 'Hirarki (Kategori, Bab, Sub-Bab)'],
                ['number' => '24/7', 'unit' => '', 'label' => 'Akses Fleksibel Kapan Saja'],
            ]), 'is_displayed' => true, 'sort_order' => 8, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'feature_section_tag', 'group' => 'section_headers', 'label' => 'Tag Section Fitur', 'type' => 'text', 'value' => 'Mengapa Forecasting Academy?', 'is_displayed' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'feature_section_title', 'group' => 'section_headers', 'label' => 'Judul Section Fitur', 'type' => 'text', 'value' => 'Fitur Cerdas yang Dirancang untuk Kelulusanmu', 'is_displayed' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'feature_section_subtitle', 'group' => 'section_headers', 'label' => 'Subjudul Section Fitur', 'type' => 'text', 'value' => 'Semua fitur terintegrasi langsung dengan riwayat pengerjaan soal dan progres belajarmu.', 'is_displayed' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'program_section_tag', 'group' => 'section_headers', 'label' => 'Tag Section Program', 'type' => 'text', 'value' => 'Kurikulum Terstruktur', 'is_displayed' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'program_section_title', 'group' => 'section_headers', 'label' => 'Judul Section Program', 'type' => 'text', 'value' => 'Fokus Program & Ambang Batas (Passing Grade)', 'is_displayed' => true, 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'program_section_subtitle', 'group' => 'section_headers', 'label' => 'Subjudul Section Program', 'type' => 'text', 'value' => 'Kurikulum yang disusun spesifik mengikuti standar seleksi terbaru CPNS, Kedinasan STIS, dan Tes Psikologi.', 'is_displayed' => true, 'sort_order' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'package_section_tag', 'group' => 'section_headers', 'label' => 'Tag Section Paket', 'type' => 'text', 'value' => 'Investasi Masa Depan', 'is_displayed' => true, 'sort_order' => 7, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'package_section_title', 'group' => 'section_headers', 'label' => 'Judul Section Paket', 'type' => 'text', 'value' => 'Pilih Paket Belajar Sesuai Kebutuhanmu', 'is_displayed' => true, 'sort_order' => 8, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'package_section_subtitle', 'group' => 'section_headers', 'label' => 'Subjudul Section Paket', 'type' => 'text', 'value' => 'Mulai dari latihan per sub-bab, paket khusus Tes Psikologi, hingga bundling lengkap video + modul + tryout.', 'is_displayed' => true, 'sort_order' => 9, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer_about', 'group' => 'footer_general', 'label' => 'Deskripsi Singkat Footer', 'type' => 'text', 'value' => 'Platform E-Learning & Smart CBT persiapan SKD, Kedinasan Politeknik Statistika STIS, SKB BPS, dan Tes Psikologi berbasis analitik.', 'is_displayed' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer_copyright', 'group' => 'footer_general', 'label' => 'Teks Hak Cipta', 'type' => 'text', 'value' => 'Forecasting Academy. All rights reserved.', 'is_displayed' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer_address', 'group' => 'footer_contact', 'label' => 'Alamat Kantor', 'type' => 'text', 'value' => 'Jl. Otto Iskandardinata No. 64C, Jatinegara, Jakarta Timur 13330', 'is_displayed' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer_whatsapp', 'group' => 'footer_contact', 'label' => 'WhatsApp CS', 'type' => 'text', 'value' => '+62 812-3456-7890 (Chat Only)', 'is_displayed' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer_email', 'group' => 'footer_contact', 'label' => 'Email Resmi', 'type' => 'text', 'value' => 'halo@forecasting-academy.test', 'is_displayed' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer_hours', 'group' => 'footer_contact', 'label' => 'Jam Operasional', 'type' => 'text', 'value' => 'Senin - Sabtu: 08.00 - 21.00 WIB (CBT Online 24 Jam)', 'is_displayed' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'social_instagram', 'group' => 'footer_social', 'label' => 'Instagram', 'type' => 'url', 'value' => 'https://instagram.com/forecasting.academy', 'is_displayed' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'social_youtube', 'group' => 'footer_social', 'label' => 'YouTube Channel', 'type' => 'url', 'value' => 'https://youtube.com/@forecastingacademy', 'is_displayed' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'social_telegram', 'group' => 'footer_social', 'label' => 'Grup Telegram Diskusi', 'type' => 'url', 'value' => 'https://t.me/forecasting_academy', 'is_displayed' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('landing_menus')->insert([
            ['title' => 'Tentang Kami', 'url' => '#tentang', 'location' => 'navbar', 'is_displayed' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Fitur Unggulan', 'url' => '#fitur', 'location' => 'navbar', 'is_displayed' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Program & Silabus', 'url' => '#program', 'location' => 'navbar', 'is_displayed' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Paket Belajar', 'url' => '#paket', 'location' => 'navbar', 'is_displayed' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Katalog Paket Bundling', 'url' => '#paket', 'location' => 'footer_quicklink', 'is_displayed' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Simulasi Tryout SKD & STIS', 'url' => '#program', 'location' => 'footer_quicklink', 'is_displayed' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Cara Kerja Competency Diagnostic', 'url' => '#fitur', 'location' => 'footer_quicklink', 'is_displayed' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('landing_features')->insert([
            ['title' => 'Competency Diagnostic Mapping', 'description' => 'Evaluasi mendalam pasca-tryout yang memetakan penguasaanmu di setiap Kategori, Bab, dan Sub-Bab menjadi status Unggul (≥75%), Cukup, atau Lemah (<50%).', 'icon_key' => 'chart', 'color_theme' => 'blue', 'is_displayed' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Pengacakan Berurutan per Kategori', 'description' => 'Soal diacak secara dinamis sesuai kuota tiap kategori namun tetap menjaga urutan baku ujian (misal: 10 TWK acak dulu, disusul 10 TIU acak, lalu 10 TKP acak).', 'icon_key' => 'layers', 'color_theme' => 'cyan', 'is_displayed' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Time-Spent Tracker per Soal', 'description' => 'Mencatat akurat berapa detik waktu yang kamu habiskan pada tiap butir soal dan berapa kali soal dikunjungi ulang dibanding waktu ideal.', 'icon_key' => 'clock', 'color_theme' => 'amber', 'is_displayed' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Simulasi Tes Psikologi Khusus', 'description' => 'Dilengkapi modul interaktif khusus untuk Tes Koran (Pauli/Kraepelin), Ganjil Genap, serta ujian 2 fase memorisasi untuk Tes Hafal Denah.', 'icon_key' => 'brain', 'color_theme' => 'emerald', 'is_displayed' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Video Materi & Pembahasan', 'description' => 'Akses ratusan video konsep dasar dan trik cepat dari mentor berpengalaman lengkap dengan pelacak progres tontonan.', 'icon_key' => 'video', 'color_theme' => 'purple', 'is_displayed' => true, 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Sesi Private 1-on-1 Mentoring', 'description' => 'Booking sesi konsultasi privat 1-on-1 langsung bersama pengajar ahli statistika atau psikolog pilihanmu.', 'icon_key' => 'users', 'color_theme' => 'rose', 'is_displayed' => true, 'sort_order' => 6, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // =====================================================================
        // 3. SEED PROGRAMS, SUBJECTS, & TOPICS
        // =====================================================================
        $progSkdId = DB::table('programs')->insertGetId([
            'name' => 'Seleksi Kompetensi Dasar (SKD)',
            'slug' => 'skd-cpns-kedinasan',
            'badge_text' => 'Fokus Utama • CPNS & Kedinasan',
            'description' => 'Program persiapan intensif TWK, TIU, dan TKP untuk CPNS & Sekolah Kedinasan dengan pemetaan Bab & Sub-Bab akurat.',
            'show_on_homepage' => true,
            'sort_order' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $progStisId = DB::table('programs')->insertGetId([
            'name' => 'Kedinasan Politeknik Statistika STIS',
            'slug' => 'kedinasan-stis',
            'badge_text' => 'Spesialisasi • Statistik & Matematika',
            'description' => 'Fokus pendalaman Matematika Lanjutan, Kalkulus, Peluang, dan Statistika Dasar khusus seleksi SPMB STIS.',
            'show_on_homepage' => true,
            'sort_order' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $progPsiId = DB::table('programs')->insertGetId([
            'name' => 'Tes Psikologi & Ketahanan Kerja',
            'slug' => 'tes-psikologi',
            'badge_text' => 'Simulasi Interaktif • Non-Kognitif',
            'description' => 'Simulasi khusus Tes Koran (Pauli/Kraepelin), Ganjil Genap, dan ujian memorisasi 2 fase untuk Tes Hafal Denah.',
            'custom_stat_label' => 'Simulasi Tes Koran, Ganjil Genap & Hafal Denah (Realtime Speed)',
            'show_on_homepage' => true,
            'sort_order' => 3,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $subjTwkId      = DB::table('subjects')->insertGetId(['program_id' => $progSkdId, 'name' => 'Tes Wawasan Kebangsaan (TWK)', 'code' => 'TWK', 'passing_grade' => 65, 'is_displayed' => true, 'created_at' => $now, 'updated_at' => $now]);
        $subjTiuId      = DB::table('subjects')->insertGetId(['program_id' => $progSkdId, 'name' => 'Tes Intelegensia Umum (TIU)', 'code' => 'TIU', 'passing_grade' => 80, 'is_displayed' => true, 'created_at' => $now, 'updated_at' => $now]);
        $subjTkpId      = DB::table('subjects')->insertGetId(['program_id' => $progSkdId, 'name' => 'Tes Karakteristik Pribadi (TKP)', 'code' => 'TKP', 'passing_grade' => 166, 'is_displayed' => true, 'created_at' => $now, 'updated_at' => $now]);
        $subjMathStisId = DB::table('subjects')->insertGetId(['program_id' => $progStisId, 'name' => 'Matematika SPMB STIS (MTK)', 'code' => 'MTK', 'passing_grade' => 75, 'is_displayed' => true, 'created_at' => $now, 'updated_at' => $now]);
        $subjPsikotesId = DB::table('subjects')->insertGetId(['program_id' => $progPsiId, 'name' => 'Ketahanan & Memorisasi Psikotes', 'code' => 'PSIKOTES', 'passing_grade' => 70, 'is_displayed' => true, 'created_at' => $now, 'updated_at' => $now]);

        // Topics: Kategori -> Bab (chapter) -> Sub Bab (name)
        $topicBelaNegaraId = DB::table('topics')->insertGetId([
            'subject_id' => $subjTwkId,
            'category' => 'TWK',
            'chapter' => 'Nasionalisme & Bela Negara',
            'name' => 'Bela Negara & Integritas Nasional',
            'description' => 'Implementasi sikap bela negara dan pilar kebangsaan.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $topicPilarNegaraId = DB::table('topics')->insertGetId([
            'subject_id' => $subjTwkId,
            'category' => 'TWK',
            'chapter' => 'Pilar Kebangsaan',
            'name' => 'Pancasila, UUD 1945 & Bhinneka Tunggal Ika',
            'description' => 'Pengamalan sila-sila Pancasila dan pasal konstitusi UUD 1945.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $topicAnalogiId = DB::table('topics')->insertGetId([
            'subject_id' => $subjTiuId,
            'category' => 'TIU',
            'chapter' => 'Kemampuan Verbal',
            'name' => 'Analogi Kata',
            'description' => 'Padanan hubungan kata dan korelasi makna antar konsep.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $topicSilogismeId = DB::table('topics')->insertGetId([
            'subject_id' => $subjTiuId,
            'category' => 'TIU',
            'chapter' => 'Kemampuan Verbal',
            'name' => 'Penalaran Logis (Silogisme)',
            'description' => 'Penarikan kesimpulan dari premis mayor, minor, dan ekuivalensi.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $topicHitungCepatId = DB::table('topics')->insertGetId([
            'subject_id' => $subjTiuId,
            'category' => 'TIU',
            'chapter' => 'Kemampuan Numerik',
            'name' => 'Hitung Cepat & Kesetaraan Bilangan',
            'description' => 'Operasi aljabar pecahan, desimal, persentase, dan pola barisan.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $topicKuantitasId = DB::table('topics')->insertGetId([
            'subject_id' => $subjTiuId,
            'category' => 'TIU',
            'chapter' => 'Kemampuan Numerik',
            'name' => 'Hubungan Kuantitas (X dan Y)',
            'description' => 'Analisis perbandingan nilai variabel kuantitatif X dan Y.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $topicFiguralId = DB::table('topics')->insertGetId([
            'subject_id' => $subjTiuId,
            'category' => 'TIU',
            'chapter' => 'Kemampuan Figural',
            'name' => 'Analogi & Rotasi Gambar',
            'description' => 'Analisis perubahan pola ruang, serial gambar, dan jaring-jaring bangun.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $topicPelayananId = DB::table('topics')->insertGetId([
            'subject_id' => $subjTkpId,
            'category' => 'TKP',
            'chapter' => 'Karakteristik Kepribadian',
            'name' => 'Pelayanan Publik & Jejaring Kerja',
            'description' => 'Orientasi solusi cepat dan empati terhadap masyarakat.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $topicProfesionalismeId = DB::table('topics')->insertGetId([
            'subject_id' => $subjTkpId,
            'category' => 'TKP',
            'chapter' => 'Karakteristik Kepribadian',
            'name' => 'Profesionalisme & Anti Radikalisme',
            'description' => 'Keteguhan memegang prinsip etika profesi ASN di bawah tekanan.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $topicPeluangStisId = DB::table('topics')->insertGetId([
            'subject_id' => $subjMathStisId,
            'category' => 'MTK',
            'chapter' => 'Statistika & Peluang',
            'name' => 'Peluang, Kombinatorika & Distribusi',
            'description' => 'Distribusi peluang, permutasi, kombinasi, dan kuartil data.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $topicDenahPsiId = DB::table('topics')->insertGetId([
            'subject_id' => $subjPsikotesId,
            'category' => 'PSIKOTES',
            'chapter' => 'Ketelitian & Memorisasi Visual',
            'name' => 'Memorisasi Hafal Denah & Tes Koran',
            'description' => 'Daya ingat visual peta arah mata angin dan ketelitian hitung cepat.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // =====================================================================
        // 4. SEED VIDEO MATERI & MODUL PEMBELAJARAN
        // =====================================================================
        $mat1 = DB::table('materials')->insertGetId(['topic_id' => $topicSilogismeId, 'instructor_id' => $mentorStisId, 'category' => 'TIU', 'chapter' => 'Kemampuan Verbal', 'sub_chapter' => 'Penalaran Logis (Silogisme)', 'title' => 'Trik Cepat Taklukkan Silogisme & Analogi Kata dalam 10 Detik', 'type' => 'video', 'content_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 900, 'is_free_preview' => true, 'created_at' => $now, 'updated_at' => $now]);
        $mat2 = DB::table('materials')->insertGetId(['topic_id' => $topicHitungCepatId, 'instructor_id' => $mentorStisId, 'category' => 'TIU', 'chapter' => 'Kemampuan Numerik', 'sub_chapter' => 'Hitung Cepat & Kesetaraan Bilangan', 'title' => 'Pola Rahasia Hitung Cepat & Hubungan Kuantitas (X dan Y)', 'type' => 'video', 'content_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 1200, 'is_free_preview' => false, 'created_at' => $now, 'updated_at' => $now]);
        $mat3 = DB::table('materials')->insertGetId(['topic_id' => $topicPeluangStisId, 'instructor_id' => $mentorStisId, 'category' => 'MTK', 'chapter' => 'Statistika & Peluang', 'sub_chapter' => 'Peluang, Kombinatorika & Distribusi', 'title' => 'Bedah Soal Peluang & Distribusi Binomial SPMB STIS', 'type' => 'video', 'content_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 1500, 'is_free_preview' => false, 'created_at' => $now, 'updated_at' => $now]);
        $mat4 = DB::table('materials')->insertGetId(['topic_id' => $topicDenahPsiId, 'instructor_id' => $mentorPsiId, 'category' => 'PSIKOTES', 'chapter' => 'Ketelitian & Memorisasi Visual', 'sub_chapter' => 'Memorisasi Hafal Denah & Tes Koran', 'title' => 'Teknik Jembatan Keledai Menghafal Denah & Grafik Koran', 'type' => 'video', 'content_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 1080, 'is_free_preview' => true, 'created_at' => $now, 'updated_at' => $now]);
        $mat5 = DB::table('materials')->insertGetId(['topic_id' => $topicPelayananId, 'instructor_id' => $mentorPsiId, 'category' => 'TKP', 'chapter' => 'Karakteristik Kepribadian', 'sub_chapter' => 'Pelayanan Publik & Jejaring Kerja', 'title' => 'Strategi Menebak Opsi Poin 5 pada Soal TKP HOTS', 'type' => 'video', 'content_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 840, 'is_free_preview' => false, 'created_at' => $now, 'updated_at' => $now]);
        $mat6 = DB::table('materials')->insertGetId(['topic_id' => $topicFiguralId, 'instructor_id' => $mentorStisId, 'category' => 'TIU', 'chapter' => 'Kemampuan Figural', 'sub_chapter' => 'Analogi & Rotasi Gambar', 'title' => 'Modul PDF Ringkasan Rumus & Rotasi Bangun Ruang Figural', 'type' => 'pdf', 'content_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 600, 'is_free_preview' => false, 'created_at' => $now, 'updated_at' => $now]);

        // =====================================================================
        // 5. KONFIGURASI 6 PAKET TRYOUT BESERTA ATURAN ACAK PER KATEGORI
        // =====================================================================
        // Sub-topik per kategori agar soal yang di-generate kaya variasi Bab & Sub-Bab
        $categorySubTopics = [
            'TWK' => [
                ['topic_id' => $topicBelaNegaraId, 'chapter' => 'Nasionalisme & Bela Negara', 'sub_chapter' => 'Bela Negara & Integritas Nasional'],
                ['topic_id' => $topicPilarNegaraId, 'chapter' => 'Pilar Kebangsaan', 'sub_chapter' => 'Pancasila, UUD 1945 & Bhinneka Tunggal Ika'],
            ],
            'TIU' => [
                ['topic_id' => $topicAnalogiId, 'chapter' => 'Kemampuan Verbal', 'sub_chapter' => 'Analogi Kata'],
                ['topic_id' => $topicSilogismeId, 'chapter' => 'Kemampuan Verbal', 'sub_chapter' => 'Penalaran Logis (Silogisme)'],
                ['topic_id' => $topicHitungCepatId, 'chapter' => 'Kemampuan Numerik', 'sub_chapter' => 'Hitung Cepat & Kesetaraan Bilangan'],
                ['topic_id' => $topicKuantitasId, 'chapter' => 'Kemampuan Numerik', 'sub_chapter' => 'Hubungan Kuantitas (X dan Y)'],
                ['topic_id' => $topicFiguralId, 'chapter' => 'Kemampuan Figural', 'sub_chapter' => 'Analogi & Rotasi Gambar'],
            ],
            'TKP' => [
                ['topic_id' => $topicPelayananId, 'chapter' => 'Karakteristik Kepribadian', 'sub_chapter' => 'Pelayanan Publik & Jejaring Kerja'],
                ['topic_id' => $topicProfesionalismeId, 'chapter' => 'Karakteristik Kepribadian', 'sub_chapter' => 'Profesionalisme & Anti Radikalisme'],
            ],
            'MTK' => [
                ['topic_id' => $topicPeluangStisId, 'chapter' => 'Statistika & Peluang', 'sub_chapter' => 'Peluang, Kombinatorika & Distribusi'],
            ],
            'PSIKOTES' => [
                ['topic_id' => $topicDenahPsiId, 'chapter' => 'Ketelitian & Memorisasi Visual', 'sub_chapter' => 'Memorisasi Hafal Denah & Tes Koran'],
            ],
        ];

        $tryoutPackagesData = [
            [
                // SESUAI CONTOH PERMINTAAN USER:
                // Pool: 15 TWK + 17 TIU + 13 TKP = 45 Soal di Bank Soal Tryout
                // Tampil Acak Berurutan: 10 TWK dulu (No 1-10), lalu 10 TIU (No 11-20), lalu 10 TKP (No 21-30) = 30 Soal
                'program_id' => $progSkdId,
                'code' => 'TO-SKD-01',
                'title' => 'Paket Tryout 1: Simulasi Akbar SKD CPNS & Kedinasan Batch #1',
                'type' => 'full_skd',
                'duration_minutes' => 60,
                'instruction' => 'Kolam soal berisi 45 soal (15 TWK, 17 TIU, 13 TKP). Sistem otomatis mengacak dan menampilkan 30 soal dengan urutan: 10 TWK (No. 1-10), disusul 10 TIU (No. 11-20), dan 10 TKP (No. 21-30).',
                'rules' => [
                    ['category' => 'TWK', 'pool_count' => 15, 'display_quota' => 10, 'passing_grade' => 30, 'sort_order' => 1],
                    ['category' => 'TIU', 'pool_count' => 17, 'display_quota' => 10, 'passing_grade' => 35, 'sort_order' => 2],
                    ['category' => 'TKP', 'pool_count' => 13, 'display_quota' => 10, 'passing_grade' => 38, 'sort_order' => 3],
                ],
            ],
            [
                // Pool: 15 TWK + 17 TIU + 13 TKP = 45 Soal -> Tampil: 10 TWK + 10 TIU + 10 TKP = 30 Soal
                'program_id' => $progSkdId,
                'code' => 'TO-SKD-02',
                'title' => 'Paket Tryout 2: Simulasi Akbar SKD Kedinasan STIS Batch #2 (HOTS)',
                'type' => 'full_skd',
                'duration_minutes' => 60,
                'instruction' => 'Standar soal HOTS terbaru BKN. Menampilkan 10 TWK acak, diikuti 10 TIU acak, dan 10 TKP acak dari total 45 soal di bank soal.',
                'rules' => [
                    ['category' => 'TWK', 'pool_count' => 15, 'display_quota' => 10, 'passing_grade' => 30, 'sort_order' => 1],
                    ['category' => 'TIU', 'pool_count' => 17, 'display_quota' => 10, 'passing_grade' => 35, 'sort_order' => 2],
                    ['category' => 'TKP', 'pool_count' => 13, 'display_quota' => 10, 'passing_grade' => 38, 'sort_order' => 3],
                ],
            ],
            [
                // Spesialisasi TIU: Pool 25 Soal TIU -> Tampil 15 Soal TIU Acak
                'program_id' => $progSkdId,
                'code' => 'TO-TIU-03',
                'title' => 'Paket Tryout 3: Spesialisasi Kemampuan Verbal, Numerik & Figural TIU',
                'type' => 'per_materi',
                'duration_minutes' => 30,
                'instruction' => 'Terdiri dari 25 soal di kolam Bank Soal TIU, diambil 15 soal secara acak setiap sesi.',
                'rules' => [
                    ['category' => 'TIU', 'pool_count' => 25, 'display_quota' => 15, 'passing_grade' => 50, 'sort_order' => 1],
                ],
            ],
            [
                // Spesialisasi MTK STIS: Pool 25 Soal MTK -> Tampil 15 Soal MTK Acak
                'program_id' => $progStisId,
                'code' => 'TO-STIS-04',
                'title' => 'Paket Tryout 4: Spesialisasi Matematika Lanjutan & Statistika SPMB STIS',
                'type' => 'per_materi',
                'duration_minutes' => 45,
                'instruction' => 'Terdiri dari 25 soal Matematika STIS di kolam soal, ditampilkan 15 soal acak.',
                'rules' => [
                    ['category' => 'MTK', 'pool_count' => 25, 'display_quota' => 15, 'passing_grade' => 50, 'sort_order' => 1],
                ],
            ],
            [
                'program_id' => $progPsiId,
                'code' => 'TO-KORAN-05',
                'title' => 'Paket Tryout 5: Lab Psikotes Kecepatan Hitung Koran & Ganjil Genap',
                'type' => 'psikologi_koran',
                'duration_minutes' => 20,
                'instruction' => 'Dari 20 soal psikotes kecepatan koran di kolam soal, ditampilkan 15 soal acak.',
                'rules' => [
                    ['category' => 'PSIKOTES', 'pool_count' => 20, 'display_quota' => 15, 'passing_grade' => 50, 'sort_order' => 1],
                ],
            ],
            [
                'program_id' => $progPsiId,
                'code' => 'TO-DENAH-06',
                'title' => 'Paket Tryout 6: Simulasi Psikotes 2 Fase Memorisasi Hafal Denah Kota',
                'type' => 'hafal_denah',
                'duration_minutes' => 25,
                'instruction' => 'Dari 20 soal memorisasi denah di kolam soal, ditampilkan 15 soal acak.',
                'rules' => [
                    ['category' => 'PSIKOTES', 'pool_count' => 20, 'display_quota' => 15, 'passing_grade' => 50, 'sort_order' => 1],
                ],
            ],
        ];

        $tryoutIds = [];
        // Menyimpan sampel soal TO-SKD-01 per kategori untuk disimulasikan ke tryout_attempts Andi Pratama
        $firstTryoutPoolByCategory = [
            'TWK' => [],
            'TIU' => [],
            'TKP' => [],
        ];

        // =====================================================================
        // 6. GENERATE POOL SOAL & ATURAN KUOTA ACAK PER KATEGORI (`tryout_category_rules`)
        // =====================================================================
        foreach ($tryoutPackagesData as $pIdx => $toData) {
            $totalPool    = collect($toData['rules'])->sum('pool_count');
            $totalDisplay = collect($toData['rules'])->sum('display_quota');

            $tryoutId = DB::table('tryouts')->insertGetId([
                'program_id'                 => $toData['program_id'],
                'code'                       => $toData['code'],
                'title'                      => $toData['title'],
                'type'                       => $toData['type'],
                'duration_minutes'           => $toData['duration_minutes'],
                'total_pool_questions'       => $totalPool,
                'total_questions'            => $totalDisplay,
                'is_randomized_per_category' => true,
                'instruction'                => $toData['instruction'],
                'is_active'                  => true,
                'created_at'                 => $now,
                'updated_at'                 => $now,
            ]);
            $tryoutIds[] = $tryoutId;

            $runningQuestionNo = 1;

            foreach ($toData['rules'] as $rule) {
                $cat = $rule['category'];

                // Simpan aturan kuota acak & urutan kategori ke tabel `tryout_category_rules`
                DB::table('tryout_category_rules')->insert([
                    'tryout_id'            => $tryoutId,
                    'category'             => $cat,
                    'available_pool_count' => $rule['pool_count'],
                    'display_quota'        => $rule['display_quota'],
                    'passing_grade'        => $rule['passing_grade'],
                    'sort_order'           => $rule['sort_order'],
                    'is_randomized'        => true,
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ]);

                $subTopics = $categorySubTopics[$cat];

                // Buat butir soal sejumlah `pool_count` (misal: 15 TWK, 17 TIU, 13 TKP)
                for ($cNo = 1; $cNo <= $rule['pool_count']; $cNo++) {
                    $st         = $subTopics[($cNo - 1) % count($subTopics)];
                    $topicId    = $st['topic_id'];
                    $chapter    = $st['chapter'];
                    $subChapter = $st['sub_chapter'];

                    $hasImage = ($runningQuestionNo % 3 === 0) || ($toData['type'] === 'hafal_denah');
                    $questionImgUrl = $hasImage
                        ? "https://placehold.co/720x320/EFF6FF/1E40AF?text=" . urlencode("{$toData['code']}+{$cat}+Pool+No+{$cNo}")
                        : null;
                    $discussionImgUrl = $hasImage
                        ? "https://placehold.co/720x300/ECFDF5/065F46?text=" . urlencode("Skema+Pembahasan+{$cat}+No+{$cNo}")
                        : null;

                    if ($subChapter === 'Analogi Kata') {
                        $qText = "[{$cat} • {$chapter} • {$subChapter} - Pool #{$cNo}] STATISTIKA : DATA = METEOROLOGI : ... Pilihlah pasangan kata yang memiliki hubungan paling setara!";
                        $ansCorrect = "CUACA (Statistika mengolah data, Meteorologi mengkaji cuaca)";
                        $discText = "Pembahasan: Hubungan kata bersifat Ilmu dan Objek Kajian Utamanya.";
                        $idealSec = 30;
                    } elseif ($subChapter === 'Hitung Cepat & Kesetaraan Bilangan') {
                        $base = $cNo * 2;
                        $qText = "[{$cat} • {$chapter} • {$subChapter} - Pool #{$cNo}] Nilai dari (" . ($base + 12) . " × 25%) + (" . ($base) . ",5 × 2) dan suku berikutnya dari deret " . ($base) . ", " . ($base + 4) . ", " . ($base + 12) . ", " . ($base + 28) . ", ... adalah?";
                        $ansCorrect = (string) ($base + 60);
                        $discText = "Pembahasan: Selisih antar suku adalah +4, +8, +16, +32. Maka suku berikutnya = " . ($base + 28) . " + 32 = " . ($base + 60) . ".";
                        $idealSec = 45;
                    } elseif ($subChapter === 'Hubungan Kuantitas (X dan Y)') {
                        $xVal = $cNo + 15;
                        $qText = "[{$cat} • {$chapter} • {$subChapter} - Pool #{$cNo}] Diketahui X adalah 35% dari " . ($xVal * 20) . " dan Y adalah 70% dari " . ($xVal * 10) . ". Manakah hubungan yang tepat antara kuantitas X dan Y?";
                        $ansCorrect = "X = Y (Nilai kuantitas X sama persis dengan nilai kuantitas Y)";
                        $discText = "Pembahasan: Sifat komutatif persentase: 35% × 20 = 70% × 10, sehingga nilai X = Y.";
                        $idealSec = 40;
                    } elseif ($subChapter === 'Penalaran Logis (Silogisme)') {
                        $qText = "[{$cat} • {$chapter} • {$subChapter} - Pool #{$cNo}] Premis 1: Semua peserta yang lulus seleksi kedinasan STIS memiliki ketelitian tinggi. Premis 2: Sebagian peserta yang memiliki ketelitian tinggi gemar berlatih Tryout CBT setiap pekan. Simpulan yang sah adalah...";
                        $ansCorrect = "Sebagian peserta yang gemar berlatih Tryout CBT memiliki ketelitian tinggi dan lulus seleksi.";
                        $discText = "Pembahasan: Berdasarkan hukum Silogisme Kategorik, simpulan wajib memuat kuantor 'Sebagian'.";
                        $idealSec = 50;
                    } elseif ($cat === 'MTK') {
                        $qText = "[{$cat} • {$chapter} • {$subChapter} - Pool #{$cNo}] Dalam sebuah kotak terdapat " . ($cNo + 3) . " bola biru dan 4 bola merah. Jika diambil 2 bola sekaligus secara acak, berapakah peluang terambilnya minimal 1 bola berwarna biru?";
                        $ansCorrect = "1 - [C(4,2) / C(" . ($cNo + 7) . ",2)]";
                        $discText = "Pembahasan: Gunakan prinsip komplemen peluang: P(Minimal 1 Biru) = 1 - P(Keduanya Merah).";
                        $idealSec = 65;
                    } elseif ($cat === 'PSIKOTES' || $subChapter === 'Analogi & Rotasi Gambar') {
                        $qText = "[{$cat} • {$chapter} • {$subChapter} - Pool #{$cNo}] Perhatikan gambar denah/diagram visual yang terlampir! Jika Anda bergerak dari Gedung Utama menuju arah Timur Laut sejauh 2 blok, bangunan apakah yang berada tepat di sebelah kiri Anda?";
                        $ansCorrect = "Gedung Pusat Komputasi Statistik (Blok C-" . $cNo . ")";
                        $discText = "Pembahasan: Saat menghadap Timur Laut, sisi kiri Anda adalah arah Barat Laut.";
                        $idealSec = 35;
                    } elseif ($cat === 'TKP') {
                        $qText = "[{$cat} • {$chapter} • {$subChapter} - Pool #{$cNo}] Anda sedang bertugas melayani masyarakat di loket pelayanan publik, namun sistem jaringan tiba-tiba melambat ketika antrean cukup panjang. Tindakan paling profesional yang Anda ambil adalah...";
                        $ansCorrect = "Menyampaikan permohonan maaf secara sopan, memberi penjelasan transparan, dan berkoordinasi dengan tim IT sembari melayani pencatatan manual.";
                        $discText = "Pembahasan: Soal TKP menuntut inisiatif solutif (Skor 5) tanpa menyalahkan pihak lain.";
                        $idealSec = 45;
                    } else {
                        $qText = "[{$cat} • {$chapter} • {$subChapter} - Pool #{$cNo}] Sikap mempertahankan integritas nasional dan pengamalan nilai Pancasila di era transformasi digital bagi seorang ASN diwujudkan melalui...";
                        $ansCorrect = "Menjaga kerahasiaan data negara dan menangkal disinformasi dengan berbasis data statistik yang valid.";
                        $discText = "Pembahasan: Bela negara non-fisik bagi ASN fokus pada kedaulatan data, integritas profesi, dan pelayanan masyarakat.";
                        $idealSec = 40;
                    }

                    $qId = DB::table('questions')->insertGetId([
                        'tryout_id'            => $tryoutId,
                        'topic_id'             => $topicId,
                        'category'             => $cat,
                        'chapter'              => $chapter,
                        'sub_chapter'          => $subChapter,
                        'question_number'      => $runningQuestionNo,
                        'question_text'        => $qText,
                        'question_image'       => $questionImgUrl,
                        'ideal_time_seconds'   => $idealSec,
                        'discussion_text'      => $discText,
                        'discussion_image'     => $discussionImgUrl,
                        'discussion_video_url' => ($cNo <= 2) ? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' : null,
                        'created_at'           => $now,
                        'updated_at'           => $now,
                    ]);

                    DB::table('tryout_questions')->insert([
                        'tryout_id'           => $tryoutId,
                        'question_id'         => $qId,
                        'category'            => $cat,
                        'category_sort_order' => $rule['sort_order'],
                        'question_number'     => $runningQuestionNo,
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ]);

                    $isTkp = ($cat === 'TKP');
                    $hasOptionImage = ($subChapter === 'Analogi & Rotasi Gambar' || ($toData['type'] === 'hafal_denah' && $cNo % 4 === 0));

                    $optionsData = [
                        ['label' => 'A', 'text' => 'Opsi A untuk soal #' . $runningQuestionNo, 'weight' => $isTkp ? 2 : 0],
                        ['label' => 'B', 'text' => $ansCorrect, 'weight' => 5], // Kunci Jawaban (Skor 5)
                        ['label' => 'C', 'text' => 'Opsi C untuk soal #' . $runningQuestionNo, 'weight' => $isTkp ? 4 : 0],
                        ['label' => 'D', 'text' => 'Opsi D untuk soal #' . $runningQuestionNo, 'weight' => $isTkp ? 3 : 0],
                        ['label' => 'E', 'text' => 'Opsi E untuk soal #' . $runningQuestionNo, 'weight' => $isTkp ? 1 : 0],
                    ];

                    $correctOptId = null;
                    $wrongOptId   = null;
                    foreach ($optionsData as $opt) {
                        $optImgUrl = $hasOptionImage
                            ? "https://placehold.co/220x140/E0F2FE/0369A1?text=" . urlencode("Gambar+Opsi+{$opt['label']}+No+{$runningQuestionNo}")
                            : null;

                        $optId = DB::table('question_options')->insertGetId([
                            'question_id'  => $qId,
                            'option_label' => $opt['label'],
                            'option_text'  => $opt['text'],
                            'option_image' => $optImgUrl,
                            'score_weight' => $opt['weight'],
                            'created_at'   => $now,
                            'updated_at'   => $now,
                        ]);
                        if ($opt['weight'] === 5) $correctOptId = $optId;
                        if ($opt['label'] === 'A') $wrongOptId = $optId;
                    }

                    // Simpan ke kolam kategori untuk simulasi pengacakan 10 TWK + 10 TIU + 10 TKP pada TO-SKD-01
                    if ($pIdx === 0 && isset($firstTryoutPoolByCategory[$cat])) {
                        $firstTryoutPoolByCategory[$cat][] = [
                            'question_id' => $qId,
                            'category'    => $cat,
                            'correct_opt' => $correctOptId,
                            'wrong_opt'   => $wrongOptId,
                        ];
                    }

                    $runningQuestionNo++;
                }
            }
        }

        // =====================================================================
        // 7. SEED PACKAGES & PACKAGE_ITEMS
        // =====================================================================
        $pkgBundlingUtama = DB::table('packages')->insertGetId([
            'name' => 'Paket Bundling Intensif SKD & STIS 2026 (Video + Modul + 4 Paket Tryout)',
            'slug' => 'bundling-intensif-skd-stis-2026',
            'type' => 'bundling',
            'badge_text' => 'Terlaris • Bundling Lengkap',
            'short_description' => 'Gabungan akses penuh Video Materi, Modul PDF, beserta 4 Paket Tryout SKD & Matematika STIS dengan sistem acak per kategori.',
            'features' => json_encode([
                'Termasuk 4 Paket Tryout (Pengacakan Berurutan TWK, TIU, TKP & MTK STIS)',
                'Termasuk Video Pembelajaran & Modul PDF Ringkasan Rumus',
                'Rapor Competency Diagnostic per Kategori, Bab & Sub-Bab',
                'Pembahasan Teks + Gambar Skema & Pelacak Detik per Soal',
            ]),
            'private_sessions_count' => 0,
            'price' => 250000,
            'discount_price' => 149000,
            'active_period_days' => 365,
            'is_featured' => true,
            'show_on_homepage' => true,
            'sort_order' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $pkgPsikologiLengkap = DB::table('packages')->insertGetId([
            'name' => 'Paket Spesialis Tes Psikologi (Tryout Koran + Hafal Denah + Video)',
            'slug' => 'paket-spesialis-tes-psikologi-lengkap',
            'type' => 'bundling',
            'badge_text' => 'Favorit • Lab Psikotes',
            'short_description' => 'Bundling khusus Paket Tryout 5 (Tes Koran), Paket Tryout 6 (Hafal Denah) beserta Video Trik Psikotes.',
            'features' => json_encode([
                'Termasuk Paket Tryout 5 (Tes Koran) & Paket Tryout 6 (Hafal Denah)',
                'Termasuk Video Teknik Menghafal Denah & TKP HOTS',
                'Ilustrasi Gambar Peta Denah & Gambar Pembahasan',
            ]),
            'private_sessions_count' => 0,
            'price' => 150000,
            'discount_price' => 89000,
            'active_period_days' => 365,
            'is_featured' => true,
            'show_on_homepage' => true,
            'sort_order' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $pkgToFullSkd = DB::table('packages')->insertGetId([
            'name' => 'Paket Tryout Murni: Full SKD & Matematika STIS',
            'slug' => 'tryout-akbar-full-skd-stis',
            'type' => 'to_full',
            'badge_text' => 'Simulasi Utuh • TO Full',
            'short_description' => 'Berisi kumpulan Paket Tryout SKD #1, SKD #2, dan Matematika STIS tanpa video materi.',
            'features' => json_encode([
                'Termasuk Paket Tryout 1, Paket Tryout 2, dan Paket Tryout 4',
                'Pengacakan Soal per Kategori (10 TWK, 10 TIU, 10 TKP)',
                'Pelacakan Waktu Ideal per Butir Soal',
            ]),
            'private_sessions_count' => 0,
            'price' => 120000,
            'discount_price' => 79000,
            'active_period_days' => 365,
            'is_featured' => false,
            'show_on_homepage' => true,
            'sort_order' => 3,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $pkgMiniTiu = DB::table('packages')->insertGetId([
            'name' => 'Mini Paket Spesialisasi TIU: Kemampuan Verbal & Numerik',
            'slug' => 'mini-to-spesialisasi-tiu',
            'type' => 'to_materi',
            'badge_text' => 'Spesialisasi • TO Per Materi',
            'short_description' => 'Fokus perbaiki Sub-Bab Analogi Kata, Hitung Cepat, dan Hubungan Kuantitas (X dan Y) yang masih Lemah.',
            'features' => json_encode([
                'Termasuk Paket Tryout 3 (Pool 25 Soal TIU -> Tampil 15 Soal Acak)',
                'Termasuk Video Trik Cepat Kemampuan Verbal & Numerik',
                'Evaluasi Akurasi & Rata-rata Kecepatan Menjawab',
            ]),
            'private_sessions_count' => 0,
            'price' => 45000,
            'discount_price' => 25000,
            'active_period_days' => 180,
            'is_featured' => false,
            'show_on_homepage' => true,
            'sort_order' => 4,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $pkgPrivate = DB::table('packages')->insertGetId([
            'name' => 'VIP Private Mentoring 1-on-1 + Semua Paket Tryout & Video',
            'slug' => 'private-mentoring-1on1-stis',
            'type' => 'private_1on1',
            'badge_text' => 'Eksklusif • Private 1-on-1',
            'short_description' => 'Bimbingan tatap muka online 4x Sesi Private 1-on-1 bersama Mentor ditambah akses seluruh Paket Tryout.',
            'features' => json_encode([
                '4x Sesi Live Mentoring Private 1-on-1 via Zoom/Google Meet',
                'Buka Semua 6 Paket Tryout',
                'Buka Semua Video Materi Pembelajaran & Modul PDF',
            ]),
            'private_sessions_count' => 4,
            'price' => 350000,
            'discount_price' => 249000,
            'active_period_days' => 365,
            'is_featured' => false,
            'show_on_homepage' => true,
            'sort_order' => 5,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('package_items')->insert([
            ['package_id' => $pkgBundlingUtama, 'item_type' => 'tryout', 'item_id' => $tryoutIds[0], 'custom_note' => 'Tryout SKD #1', 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgBundlingUtama, 'item_type' => 'tryout', 'item_id' => $tryoutIds[1], 'custom_note' => 'Tryout SKD #2', 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgBundlingUtama, 'item_type' => 'tryout', 'item_id' => $tryoutIds[2], 'custom_note' => 'Tryout Spesialisasi TIU', 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgBundlingUtama, 'item_type' => 'tryout', 'item_id' => $tryoutIds[3], 'custom_note' => 'Tryout Matematika STIS', 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgBundlingUtama, 'item_type' => 'video', 'item_id' => $mat1, 'custom_note' => 'Video Verbal TIU', 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgBundlingUtama, 'item_type' => 'video', 'item_id' => $mat2, 'custom_note' => 'Video Numerik TIU', 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgBundlingUtama, 'item_type' => 'video', 'item_id' => $mat3, 'custom_note' => 'Video Peluang STIS', 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgBundlingUtama, 'item_type' => 'pdf', 'item_id' => $mat6, 'custom_note' => 'Modul PDF Figural', 'created_at' => $now, 'updated_at' => $now],

            ['package_id' => $pkgPsikologiLengkap, 'item_type' => 'tryout', 'item_id' => $tryoutIds[4], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPsikologiLengkap, 'item_type' => 'tryout', 'item_id' => $tryoutIds[5], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPsikologiLengkap, 'item_type' => 'video', 'item_id' => $mat4, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPsikologiLengkap, 'item_type' => 'video', 'item_id' => $mat5, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],

            ['package_id' => $pkgToFullSkd, 'item_type' => 'tryout', 'item_id' => $tryoutIds[0], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgToFullSkd, 'item_type' => 'tryout', 'item_id' => $tryoutIds[1], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgToFullSkd, 'item_type' => 'tryout', 'item_id' => $tryoutIds[3], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],

            ['package_id' => $pkgMiniTiu, 'item_type' => 'tryout', 'item_id' => $tryoutIds[2], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgMiniTiu, 'item_type' => 'video', 'item_id' => $mat1, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgMiniTiu, 'item_type' => 'video', 'item_id' => $mat2, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],

            ['package_id' => $pkgPrivate, 'item_type' => 'private_1on1', 'item_id' => $mentorStisId, 'custom_note' => '4x Sesi Live Mentoring 1-on-1 Matematika STIS & TIU', 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPrivate, 'item_type' => 'private_1on1', 'item_id' => $mentorPsiId, 'custom_note' => 'Konsultasi Personal Evaluasi Psikotes & Manajemen Waktu', 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPrivate, 'item_type' => 'tryout', 'item_id' => $tryoutIds[0], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPrivate, 'item_type' => 'tryout', 'item_id' => $tryoutIds[3], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPrivate, 'item_type' => 'tryout', 'item_id' => $tryoutIds[5], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPrivate, 'item_type' => 'video', 'item_id' => $mat1, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPrivate, 'item_type' => 'video', 'item_id' => $mat3, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // =====================================================================
        // 8. SEED TRANSAKSI, AKSES & SIMULASI PENGERJAAN (10 TWK ACAK -> 10 TIU ACAK -> 10 TKP ACAK)
        // =====================================================================
        DB::table('transactions')->insert([
            ['user_id' => $studentAndiId, 'package_id' => $pkgBundlingUtama, 'invoice_code' => 'INV-FA-20261001-001', 'total_amount' => 149000, 'payment_status' => 'paid', 'payment_method' => 'QRIS', 'paid_at' => $now->copy()->subDays(15), 'created_at' => $now->copy()->subDays(15), 'updated_at' => $now],
            ['user_id' => $studentAndiId, 'package_id' => $pkgPsikologiLengkap, 'invoice_code' => 'INV-FA-20261010-002', 'total_amount' => 89000, 'payment_status' => 'paid', 'payment_method' => 'BCA VA', 'paid_at' => $now->copy()->subDays(8), 'created_at' => $now->copy()->subDays(8), 'updated_at' => $now],
            ['user_id' => $studentCitraId, 'package_id' => $pkgPrivate, 'invoice_code' => 'INV-FA-20261012-003', 'total_amount' => 249000, 'payment_status' => 'paid', 'payment_method' => 'Mandiri VA', 'paid_at' => $now->copy()->subDays(5), 'created_at' => $now->copy()->subDays(5), 'updated_at' => $now],
        ]);

        DB::table('user_accesses')->insert([
            ['user_id' => $studentAndiId, 'package_id' => $pkgBundlingUtama, 'expires_at' => $now->copy()->addDays(350), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $studentAndiId, 'package_id' => $pkgPsikologiLengkap, 'expires_at' => $now->copy()->addDays(357), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $studentCitraId, 'package_id' => $pkgPrivate, 'expires_at' => $now->copy()->addDays(360), 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Simulasikan Pengacakan Berurutan per Kategori untuk Sesi Ujian Andi Pratama pada TO-SKD-01:
        // 1. Ambil 10 TWK acak dari 15 TWK
        // 2. Ambil 10 TIU acak dari 17 TIU
        // 3. Ambil 10 TKP acak dari 13 TKP
        $selectedTwk = collect($firstTryoutPoolByCategory['TWK'])->shuffle()->take(10)->values();
        $selectedTiu = collect($firstTryoutPoolByCategory['TIU'])->shuffle()->take(10)->values();
        $selectedTkp = collect($firstTryoutPoolByCategory['TKP'])->shuffle()->take(10)->values();

        // Gabungkan dengan urutan baku: 10 TWK dulu, 10 TIU kedua, 10 TKP ketiga (Total 30 soal)
        $orderedAttemptQuestions = $selectedTwk->concat($selectedTiu)->concat($selectedTkp)->values();
        $snapshotIds = $orderedAttemptQuestions->pluck('question_id')->toArray();

        $attAndi1 = DB::table('tryout_attempts')->insertGetId([
            'user_id'               => $studentAndiId,
            'tryout_id'             => $tryoutIds[0],
            'question_snapshot_ids' => json_encode($snapshotIds),
            'started_at'            => $now->copy()->subDays(7),
            'finished_at'           => $now->copy()->subDays(7)->addMinutes(42),
            'total_score'           => 125,
            'is_passed'             => true,
            'created_at'            => $now,
            'updated_at'            => $now,
        ]);

        foreach ($orderedAttemptQuestions as $idx => $sq) {
            $assignedNo = $idx + 1; // Nomor 1-10 = TWK, Nomor 11-20 = TIU, Nomor 21-30 = TKP
            $isCorrect  = ($idx % 3 !== 0);
            DB::table('tryout_answers')->insert([
                'tryout_attempt_id'  => $attAndi1,
                'question_id'        => $sq['question_id'],
                'question_option_id' => $isCorrect ? $sq['correct_opt'] : $sq['wrong_opt'],
                'category'           => $sq['category'],
                'assigned_number'    => $assignedNo,
                'score_earned'       => $isCorrect ? 5 : ($sq['category'] === 'TKP' ? 2 : 0),
                'time_spent_seconds' => $isCorrect ? 38 : 85,
                'visit_count'        => $isCorrect ? 1 : 2,
                'is_doubtful'        => !$isCorrect,
                'first_opened_at'    => $now,
                'last_answered_at'   => $now,
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
        }

        DB::table('competency_diagnostics')->insert([
            [
                'user_id' => $studentAndiId,
                'topic_id' => $topicHitungCepatId,
                'category' => 'TIU',
                'chapter' => 'Kemampuan Numerik',
                'sub_chapter' => 'Hitung Cepat & Kesetaraan Bilangan',
                'total_answered' => 20,
                'total_correct' => 18,
                'accuracy_percentage' => 90.0,
                'total_time_seconds' => 760,
                'avg_time_seconds' => 38.0,
                'status_label' => 'Unggul',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $studentAndiId,
                'topic_id' => $topicKuantitasId,
                'category' => 'TIU',
                'chapter' => 'Kemampuan Numerik',
                'sub_chapter' => 'Hubungan Kuantitas (X dan Y)',
                'total_answered' => 20,
                'total_correct' => 17,
                'accuracy_percentage' => 85.0,
                'total_time_seconds' => 600,
                'avg_time_seconds' => 30.0,
                'status_label' => 'Unggul',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $studentAndiId,
                'topic_id' => $topicAnalogiId,
                'category' => 'TIU',
                'chapter' => 'Kemampuan Verbal',
                'sub_chapter' => 'Analogi Kata',
                'total_answered' => 20,
                'total_correct' => 13,
                'accuracy_percentage' => 65.0,
                'total_time_seconds' => 960,
                'avg_time_seconds' => 48.0,
                'status_label' => 'Cukup',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $studentAndiId,
                'topic_id' => $topicSilogismeId,
                'category' => 'TIU',
                'chapter' => 'Kemampuan Verbal',
                'sub_chapter' => 'Penalaran Logis (Silogisme)',
                'total_answered' => 20,
                'total_correct' => 7,
                'accuracy_percentage' => 35.0,
                'total_time_seconds' => 1760,
                'avg_time_seconds' => 88.0,
                'status_label' => 'Lemah',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $studentAndiId,
                'topic_id' => $topicPeluangStisId,
                'category' => 'MTK',
                'chapter' => 'Statistika & Peluang',
                'sub_chapter' => 'Peluang, Kombinatorika & Distribusi',
                'total_answered' => 20,
                'total_correct' => 8,
                'accuracy_percentage' => 40.0,
                'total_time_seconds' => 1900,
                'avg_time_seconds' => 95.0,
                'status_label' => 'Lemah',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}