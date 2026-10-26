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
            ['key' => 'hero_description', 'group' => 'hero', 'label' => 'Deskripsi Hero', 'type' => 'text', 'value' => 'Forecasting Academy menggabungkan Video Pembelajaran, Simulasi CBT Akurat, serta teknologi Competency Diagnostic & Time-Spent Analytics untuk mendiagnosis kelemahanmu hingga ke level sub-topik dan kecepatan menjawab per detik.', 'is_displayed' => true, 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'hero_primary_btn', 'group' => 'hero', 'label' => 'Teks Tombol Utama Hero', 'type' => 'text', 'value' => 'Lihat Paket Belajar', 'is_displayed' => true, 'sort_order' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'hero_secondary_btn', 'group' => 'hero', 'label' => 'Teks Tombol Kedua Hero', 'type' => 'text', 'value' => 'Pelajari Cara Kerja', 'is_displayed' => true, 'sort_order' => 7, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'hero_stats', 'group' => 'hero', 'label' => 'Statistik Hero', 'type' => 'json', 'value' => json_encode([
                ['number' => '54', 'unit' => 'dtk', 'label' => 'Pelacakan Waktu Ideal/Soal'],
                ['number' => '3', 'unit' => 'Level', 'label' => 'Pemetaan (Unggul, Cukup, Lemah)'],
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
            ['key' => 'package_section_subtitle', 'group' => 'section_headers', 'label' => 'Subjudul Section Paket', 'type' => 'text', 'value' => 'Mulai dari latihan per sub-materi, paket khusus Tes Psikologi, hingga bundling lengkap video + tryout.', 'is_displayed' => true, 'sort_order' => 9, 'created_at' => $now, 'updated_at' => $now],
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
            ['title' => 'Competency Diagnostic Mapping', 'description' => 'Evaluasi mendalam pasca-tryout yang memetakan penguasaanmu di setiap sub-topik menjadi status Unggul (≥75%), Cukup, atau Lemah (<50%).', 'icon_key' => 'chart', 'color_theme' => 'blue', 'is_displayed' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Time-Spent Tracker per Soal', 'description' => 'Mencatat akurat berapa detik waktu yang kamu habiskan pada tiap butir soal dan berapa kali soal dikunjungi ulang dibanding waktu ideal.', 'icon_key' => 'clock', 'color_theme' => 'amber', 'is_displayed' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Simulasi Tes Psikologi Khusus', 'description' => 'Dilengkapi modul interaktif khusus untuk Tes Koran (Pauli/Kraepelin), Ganjil Genap, serta ujian 2 fase memorisasi untuk Tes Hafal Denah.', 'icon_key' => 'brain', 'color_theme' => 'emerald', 'is_displayed' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Video Materi & Pembahasan', 'description' => 'Akses ratusan video konsep dasar dan trik cepat dari mentor berpengalaman lengkap dengan pelacak progres tontonan.', 'icon_key' => 'video', 'color_theme' => 'purple', 'is_displayed' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Spesialisasi Soal & Opsi Fleksibel', 'description' => 'Bebas memilih latihan mulai dari Mini TO per Materi untuk menambal topik yang lemah, TO Full SKD, hingga Paket Khusus Psikotes.', 'icon_key' => 'layers', 'color_theme' => 'cyan', 'is_displayed' => true, 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Sesi Private 1-on-1 Mentoring', 'description' => 'Booking sesi konsultasi privat 1-on-1 langsung bersama pengajar ahli statistika atau psikolog pilihanmu.', 'icon_key' => 'users', 'color_theme' => 'rose', 'is_displayed' => true, 'sort_order' => 6, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // =====================================================================
        // 3. SEED PROGRAMS, SUBJECTS, & TOPICS
        // =====================================================================
        $progSkdId = DB::table('programs')->insertGetId([
            'name' => 'Seleksi Kompetensi Dasar (SKD)',
            'slug' => 'skd-cpns-kedinasan',
            'badge_text' => 'Fokus Utama • CPNS & Kedinasan',
            'description' => 'Program persiapan intensif TWK, TIU, dan TKP untuk CPNS & Sekolah Kedinasan dengan pemetaan sub-topik akurat.',
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

        $subjTwkId      = DB::table('subjects')->insertGetId(['program_id' => $progSkdId, 'name' => 'Tes Wawasan Kebangsaan (TWK)', 'passing_grade' => 65, 'is_displayed' => true, 'created_at' => $now, 'updated_at' => $now]);
        $subjTiuId      = DB::table('subjects')->insertGetId(['program_id' => $progSkdId, 'name' => 'Tes Intelegensia Umum (TIU)', 'passing_grade' => 80, 'is_displayed' => true, 'created_at' => $now, 'updated_at' => $now]);
        $subjTkpId      = DB::table('subjects')->insertGetId(['program_id' => $progSkdId, 'name' => 'Tes Karakteristik Pribadi (TKP)', 'passing_grade' => 166, 'is_displayed' => true, 'created_at' => $now, 'updated_at' => $now]);
        $subjMathStisId = DB::table('subjects')->insertGetId(['program_id' => $progStisId, 'name' => 'Matematika SPMB STIS', 'passing_grade' => 75, 'is_displayed' => true, 'created_at' => $now, 'updated_at' => $now]);
        $subjPsikotesId = DB::table('subjects')->insertGetId(['program_id' => $progPsiId, 'name' => 'Ketahanan & Memorisasi Psikotes', 'passing_grade' => 70, 'is_displayed' => true, 'created_at' => $now, 'updated_at' => $now]);

        $topicBelaNegaraId  = DB::table('topics')->insertGetId(['subject_id' => $subjTwkId, 'name' => 'Bela Negara & Integritas Nasional', 'description' => 'Implementasi sikap bela negara dan pilar kebangsaan.', 'created_at' => $now, 'updated_at' => $now]);
        $topicSilogismeId   = DB::table('topics')->insertGetId(['subject_id' => $subjTiuId, 'name' => 'Penalaran Logis (Silogisme)', 'description' => 'Penarikan kesimpulan dari premis mayor, minor, dan ekuivalensi.', 'created_at' => $now, 'updated_at' => $now]);
        $topicDeretId       = DB::table('topics')->insertGetId(['subject_id' => $subjTiuId, 'name' => 'Numerik - Deret & Barisan Angka', 'description' => 'Pola barisan bilangan aritmatika dan geometri bertingkat.', 'created_at' => $now, 'updated_at' => $now]);
        $topicFiguralId     = DB::table('topics')->insertGetId(['subject_id' => $subjTiuId, 'name' => 'Figural - Analogi & Rotasi Gambar', 'description' => 'Analisis perubahan pola ruang dan jaring-jaring bangun.', 'created_at' => $now, 'updated_at' => $now]);
        $topicPelayananId   = DB::table('topics')->insertGetId(['subject_id' => $subjTkpId, 'name' => 'Pelayanan Publik & Jejaring Kerja', 'description' => 'Orientasi solusi cepat dan empati terhadap masyarakat.', 'created_at' => $now, 'updated_at' => $now]);
        $topicPeluangStisId = DB::table('topics')->insertGetId(['subject_id' => $subjMathStisId, 'name' => 'Peluang, Kombinatorika & Statistika', 'description' => 'Distribusi peluang, permutasi, kombinasi, dan kuartil data.', 'created_at' => $now, 'updated_at' => $now]);
        $topicDenahPsiId    = DB::table('topics')->insertGetId(['subject_id' => $subjPsikotesId, 'name' => 'Memorisasi Hafal Denah & Tes Koran', 'description' => 'Daya ingat visual peta arah mata angin dan ketelitian hitung cepat.', 'created_at' => $now, 'updated_at' => $now]);

        // =====================================================================
        // 4. SEED VIDEO MATERI & MODUL PEMBELAJARAN
        // =====================================================================
        $mat1 = DB::table('materials')->insertGetId(['topic_id' => $topicSilogismeId, 'instructor_id' => $mentorStisId, 'title' => 'Trik Cepat Taklukkan Silogisme & Ekuivalensi dalam 10 Detik', 'type' => 'video', 'content_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 900, 'is_free_preview' => true, 'created_at' => $now, 'updated_at' => $now]);
        $mat2 = DB::table('materials')->insertGetId(['topic_id' => $topicDeretId, 'instructor_id' => $mentorStisId, 'title' => 'Pola Rahasia Deret Angka TIU SKD & Matematika STIS', 'type' => 'video', 'content_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 1200, 'is_free_preview' => false, 'created_at' => $now, 'updated_at' => $now]);
        $mat3 = DB::table('materials')->insertGetId(['topic_id' => $topicPeluangStisId, 'instructor_id' => $mentorStisId, 'title' => 'Bedah Soal Peluang & Distribusi Binomial SPMB STIS', 'type' => 'video', 'content_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 1500, 'is_free_preview' => false, 'created_at' => $now, 'updated_at' => $now]);
        $mat4 = DB::table('materials')->insertGetId(['topic_id' => $topicDenahPsiId, 'instructor_id' => $mentorPsiId, 'title' => 'Teknik Jembatan Keledai Menghafal Denah & Grafik Koran', 'type' => 'video', 'content_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 1080, 'is_free_preview' => true, 'created_at' => $now, 'updated_at' => $now]);
        $mat5 = DB::table('materials')->insertGetId(['topic_id' => $topicPelayananId, 'instructor_id' => $mentorPsiId, 'title' => 'Strategi Menebak Opsi Poin 5 pada Soal TKP HOTS', 'type' => 'video', 'content_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 840, 'is_free_preview' => false, 'created_at' => $now, 'updated_at' => $now]);
        $mat6 = DB::table('materials')->insertGetId(['topic_id' => $topicFiguralId, 'instructor_id' => $mentorStisId, 'title' => 'Modul PDF & Video Rotasi Bangun Ruang Figural', 'type' => 'pdf', 'content_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 600, 'is_free_preview' => false, 'created_at' => $now, 'updated_at' => $now]);

        // =====================================================================
        // 5. SEED 6 MASTER PAKET TRYOUT (INDUK DARI BANK SOAL)
        // =====================================================================
        $tryoutPackagesData = [
            [
                'program_id' => $progSkdId,
                'code' => 'TO-SKD-01',
                'title' => 'Paket Tryout 1: Simulasi Akbar SKD CPNS & Kedinasan Batch #1',
                'type' => 'full_skd',
                'duration_minutes' => 45,
                'total_questions' => 20,
                'instruction' => 'Terdiri dari 20 butir soal pilihan (TWK, TIU, TKP). Dilengkapi gambar ilustrasi soal dan gambar skema pembahasan.',
                'default_topic_id' => $topicDeretId,
            ],
            [
                'program_id' => $progSkdId,
                'code' => 'TO-SKD-02',
                'title' => 'Paket Tryout 2: Simulasi Akbar SKD Kedinasan STIS Batch #2 (HOTS)',
                'type' => 'full_skd',
                'duration_minutes' => 45,
                'total_questions' => 20,
                'instruction' => 'Standar soal HOTS terbaru BKN dengan pelacak waktu ideal per butir soal.',
                'default_topic_id' => $topicBelaNegaraId,
            ],
            [
                'program_id' => $progSkdId,
                'code' => 'TO-TIU-03',
                'title' => 'Paket Tryout 3: Spesialisasi Penalaran Logis, Deret & Figural TIU',
                'type' => 'per_materi',
                'duration_minutes' => 30,
                'total_questions' => 20,
                'instruction' => 'Fokus evaluasi pemetaan kompetensi (Diagnostic) untuk menambal sub-tes TIU.',
                'default_topic_id' => $topicSilogismeId,
            ],
            [
                'program_id' => $progStisId,
                'code' => 'TO-STIS-04',
                'title' => 'Paket Tryout 4: Spesialisasi Matematika Lanjutan & Statistika SPMB STIS',
                'type' => 'per_materi',
                'duration_minutes' => 60,
                'total_questions' => 20,
                'instruction' => 'Mencakup soal Peluang, Kombinatorika, Kalkulus, dan Statistika Deskriptif standar STIS.',
                'default_topic_id' => $topicPeluangStisId,
            ],
            [
                'program_id' => $progPsiId,
                'code' => 'TO-KORAN-05',
                'title' => 'Paket Tryout 5: Lab Psikotes Kecepatan Hitung Koran & Ganjil Genap',
                'type' => 'psikologi_koran',
                'duration_minutes' => 20,
                'total_questions' => 20,
                'instruction' => 'Uji kecepatan dan ketelitian penjumlahan deret angka cepat di bawah tekanan waktu.',
                'default_topic_id' => $topicDenahPsiId,
            ],
            [
                'program_id' => $progPsiId,
                'code' => 'TO-DENAH-06',
                'title' => 'Paket Tryout 6: Simulasi Psikotes 2 Fase Memorisasi Hafal Denah Kota',
                'type' => 'hafal_denah',
                'duration_minutes' => 25,
                'total_questions' => 20,
                'instruction' => 'Perhatikan gambar peta/denah yang dilampirkan pada soal, lalu jawab arah dan tata letak gedung dengan tepat.',
                'default_topic_id' => $topicFiguralId,
            ],
        ];

        $tryoutIds = [];
        $allTopicIds = [$topicBelaNegaraId, $topicSilogismeId, $topicDeretId, $topicFiguralId, $topicPelayananId, $topicPeluangStisId, $topicDenahPsiId];
        $firstTryoutSampleQuestions = [];

        // =====================================================================
        // 6. GENERATE 20 SOAL PER PAKET TRYOUT (6 x 20 = 120 SOAL + GAMBAR & PEMBAHASAN)
        // =====================================================================
        foreach ($tryoutPackagesData as $pIdx => $toData) {
            $tryoutId = DB::table('tryouts')->insertGetId([
                'program_id'       => $toData['program_id'],
                'code'             => $toData['code'],
                'title'            => $toData['title'],
                'type'             => $toData['type'],
                'duration_minutes' => $toData['duration_minutes'],
                'total_questions'  => 20,
                'instruction'      => $toData['instruction'],
                'is_active'        => true,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
            $tryoutIds[] = $tryoutId;

            for ($no = 1; $no <= 20; $no++) {
                // Variasikan topik agar Competency Diagnostic kaya data
                $topicId = ($pIdx <= 1) ? $allTopicIds[($no - 1) % count($allTopicIds)] : $toData['default_topic_id'];

                // Sebagian soal (misal nomor kelipatan 3 atau paket denah/figural) dilengkapi Link Gambar Soal & Gambar Pembahasan
                $hasImage = ($no % 3 === 0) || ($toData['type'] === 'hafal_denah');
                $questionImgUrl = $hasImage
                    ? "https://placehold.co/720x320/EFF6FF/1E40AF?text=" . urlencode("Gambar+Soal+{$toData['code']}+No+{$no}")
                    : null;
                $discussionImgUrl = $hasImage
                    ? "https://placehold.co/720x300/ECFDF5/065F46?text=" . urlencode("Skema+Pembahasan+{$toData['code']}+No+{$no}")
                    : null;

                // Buat variasi teks soal dan pembahasan sesuai kategori
                if ($topicId === $topicDeretId) {
                    $base = $no * 2;
                    $qText = "[{$toData['code']} - Soal #{$no}] Perhatikan pola barisan bilangan berikut: " . ($base) . ", " . ($base + 4) . ", " . ($base + 12) . ", " . ($base + 28) . ", ... Nilai suku berikutnya yang paling tepat adalah?";
                    $ansCorrect = (string) ($base + 60);
                    $discText = "Pembahasan Soal #{$no}: Selisih antar suku adalah +4, +8, +16, maka selisih selanjutnya adalah +32. Sehingga suku berikutnya = " . ($base + 28) . " + 32 = " . ($base + 60) . ". Gunakan cara cepat melihat pola kelipatan dua.";
                    $idealSec = 45;
                } elseif ($topicId === $topicSilogismeId) {
                    $qText = "[{$toData['code']} - Soal #{$no}] Premis 1: Semua peserta yang lulus seleksi kedinasan STIS memiliki ketelitian tinggi. Premis 2: Sebagian peserta yang memiliki ketelitian tinggi gemar berlatih Tryout CBT setiap pekan. Simpulan logis yang sah adalah...";
                    $ansCorrect = "Sebagian peserta yang gemar berlatih Tryout CBT memiliki ketelitian tinggi dan lulus seleksi.";
                    $discText = "Pembahasan Soal #{$no}: Berdasarkan hukum penarikan kesimpulan Silogisme Kategorik (Kuartor Eksistensial 'Sebagian'), maka simpulan wajib memuat kata 'Sebagian'.";
                    $idealSec = 50;
                } elseif ($topicId === $topicPeluangStisId) {
                    $qText = "[{$toData['code']} - Soal #{$no}] Dalam sebuah kotak terdapat " . ($no + 3) . " bola biru dan 4 bola merah. Jika diambil 2 bola sekaligus secara acak, berapakah peluang terambilnya minimal 1 bola berwarna biru?";
                    $ansCorrect = "1 - [C(4,2) / C(" . ($no + 7) . ",2)]";
                    $discText = "Pembahasan Soal #{$no}: Gunakan prinsip komplemen peluang: P(Minimal 1 Biru) = 1 - P(Keduanya Merah) = 1 - [C(4,2) / C(" . ($no + 7) . ",2)].";
                    $idealSec = 65;
                } elseif ($topicId === $topicDenahPsiId || $topicId === $topicFiguralId) {
                    $qText = "[{$toData['code']} - Soal #{$no}] Perhatikan gambar denah/diagram visual yang terlampir di atas! Jika Anda bergerak dari Gedung Utama menuju arah Timur Laut sejauh 2 blok, bangunan apakah yang berada tepat di sebelah kiri Anda?";
                    $ansCorrect = "Gedung Pusat Komputasi Statistik (Blok C-" . $no . ")";
                    $discText = "Pembahasan Soal #{$no}: Perhatikan gambar skema pembahasan! Saat menghadap Timur Laut, sisi kiri Anda adalah arah Barat Laut yang menunjuk langsung ke Gedung Pusat Komputasi Statistik.";
                    $idealSec = 35;
                } elseif ($topicId === $topicPelayananId) {
                    $qText = "[{$toData['code']} - Soal #{$no}] Anda sedang bertugas melayani masyarakat di loket pelayanan publik, namun sistem jaringan tiba-tiba melambat ketika antrean cukup panjang. Tindakan paling profesional yang Anda ambil adalah...";
                    $ansCorrect = "Menyampaikan permohonan maaf secara sopan, memberi penjelasan transparan, dan berkoordinasi dengan tim IT sembari melayani pencatatan manual.";
                    $discText = "Pembahasan Soal #{$no}: Soal TKP jejaring kerja & pelayanan publik menuntut inisiatif solutif (Skor 5) tanpa menyalahkan pihak lain.";
                    $idealSec = 45;
                } else {
                    $qText = "[{$toData['code']} - Soal #{$no}] Sikap mempertahankan integritas nasional dan bela negara di era transformasi digital bagi seorang Aparatur Sipil Negara (ASN) diwujudkan melalui...";
                    $ansCorrect = "Menjaga kerahasiaan data negara dan menangkal disinformasi dengan berbasis data statistik yang valid.";
                    $discText = "Pembahasan Soal #{$no}: Bela negara non-fisik bagi ASN di instansi modern fokus pada kedaulatan data, integritas profesi, dan pelayanan masyarakat.";
                    $idealSec = 40;
                }

                $qId = DB::table('questions')->insertGetId([
                    'tryout_id'            => $tryoutId,
                    'topic_id'             => $topicId,
                    'question_number'      => $no,
                    'question_text'        => $qText,
                    'question_image'       => $questionImgUrl,
                    'ideal_time_seconds'   => $idealSec,
                    'discussion_text'      => $discText,
                    'discussion_image'     => $discussionImgUrl,
                    'discussion_video_url' => ($no <= 3) ? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' : null,
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ]);

                // Buat 5 Opsi Jawaban (A, B, C, D, E) — Jika soal Figural/Denah, sertakan juga contoh Link Gambar Opsi Jawaban!
                $isTkp = ($topicId === $topicPelayananId);
                $hasOptionImage = ($topicId === $topicFiguralId || ($toData['type'] === 'hafal_denah' && $no % 4 === 0));

                $optionsData = [
                    ['label' => 'A', 'text' => 'Opsi A untuk soal #' . $no, 'weight' => $isTkp ? 2 : 0],
                    ['label' => 'B', 'text' => $ansCorrect, 'weight' => 5], // Jawaban Benar
                    ['label' => 'C', 'text' => 'Opsi C untuk soal #' . $no, 'weight' => $isTkp ? 4 : 0],
                    ['label' => 'D', 'text' => 'Opsi D untuk soal #' . $no, 'weight' => $isTkp ? 3 : 0],
                    ['label' => 'E', 'text' => 'Opsi E untuk soal #' . $no, 'weight' => $isTkp ? 1 : 0],
                ];

                $correctOptId = null;
                $wrongOptId   = null;
                foreach ($optionsData as $opt) {
                    $optImgUrl = $hasOptionImage
                        ? "https://placehold.co/220x140/E0F2FE/0369A1?text=" . urlencode("Gambar+Opsi+{$opt['label']}+No+{$no}")
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

                if ($pIdx === 0 && $no <= 6) {
                    $firstTryoutSampleQuestions[] = [
                        'question_id' => $qId,
                        'correct_opt' => $correctOptId,
                        'wrong_opt'   => $wrongOptId,
                    ];
                }
            }
        }

        // =====================================================================
        // 7. SEED KATALOG PAKET BELAJAR & ISI BUNDLING DARI TRYOUT, MATERI & PRIVATE 1-ON-1
        // =====================================================================
        $pkgBundlingUtama = DB::table('packages')->insertGetId([
            'name' => 'Paket Bundling Intensif SKD & STIS 2026 (Video + 4 Paket Tryout)',
            'slug' => 'bundling-intensif-skd-stis-2026',
            'type' => 'bundling',
            'badge_text' => 'Terlaris • Bundling Lengkap',
            'short_description' => 'Gabungan akses penuh Video Materi pilihan beserta 4 Paket Tryout SKD & Matematika STIS (80 Soal).',
            'features' => json_encode([
                'Termasuk 4 Paket Tryout (SKD #1, SKD #2, Spesialisasi TIU & Matematika STIS)',
                'Termasuk 4 Video Materi Pembelajaran & Modul PDF',
                'Rapor Competency Diagnostic (Unggul/Cukup/Lemah)',
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
                'Analisis Competency Diagnostic Penuh per Sub-Topik',
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
            'name' => 'Mini Paket Spesialisasi: Logika, Silogisme & Deret TIU',
            'slug' => 'mini-to-spesialisasi-tiu',
            'type' => 'to_materi',
            'badge_text' => 'Spesialisasi • TO Per Materi',
            'short_description' => 'Fokus perbaiki topik TIU yang masih Lemah dengan Paket Tryout 3 + Video Trik Cepat TIU.',
            'features' => json_encode([
                'Termasuk Paket Tryout 3 (Spesialisasi TIU 20 Soal)',
                'Termasuk Video Trik Cepat Silogisme & Deret Angka',
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
            'short_description' => 'Bimbingan tatap muka online 4x Sesi Private 1-on-1 bersama Mentor ditambah akses seluruh 6 Paket Tryout.',
            'features' => json_encode([
                '4x Sesi Live Mentoring Private 1-on-1 via Zoom/Google Meet',
                'Buka Semua 6 Paket Tryout (120 Soal Lengkap)',
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

        // Hubungkan Paket dengan Isi Bundling-nya (package_items) dari Tryout, Materi, & Private 1-on-1
        DB::table('package_items')->insert([
            // Isi Bundling Utama (4 Tryout + 4 Video Materi)
            ['package_id' => $pkgBundlingUtama, 'item_type' => 'tryout', 'item_id' => $tryoutIds[0], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgBundlingUtama, 'item_type' => 'tryout', 'item_id' => $tryoutIds[1], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgBundlingUtama, 'item_type' => 'tryout', 'item_id' => $tryoutIds[2], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgBundlingUtama, 'item_type' => 'tryout', 'item_id' => $tryoutIds[3], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgBundlingUtama, 'item_type' => 'material', 'item_id' => $mat1, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgBundlingUtama, 'item_type' => 'material', 'item_id' => $mat2, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgBundlingUtama, 'item_type' => 'material', 'item_id' => $mat3, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgBundlingUtama, 'item_type' => 'material', 'item_id' => $mat6, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],

            // Isi Paket Psikologi (Tryout 5 & 6 + Video 4 & 5)
            ['package_id' => $pkgPsikologiLengkap, 'item_type' => 'tryout', 'item_id' => $tryoutIds[4], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPsikologiLengkap, 'item_type' => 'tryout', 'item_id' => $tryoutIds[5], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPsikologiLengkap, 'item_type' => 'material', 'item_id' => $mat4, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPsikologiLengkap, 'item_type' => 'material', 'item_id' => $mat5, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],

            // Isi Paket TO Full (Tryout 1, 2, 4)
            ['package_id' => $pkgToFullSkd, 'item_type' => 'tryout', 'item_id' => $tryoutIds[0], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgToFullSkd, 'item_type' => 'tryout', 'item_id' => $tryoutIds[1], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgToFullSkd, 'item_type' => 'tryout', 'item_id' => $tryoutIds[3], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],

            // Isi Paket Mini TIU (Tryout 3 + Video 1 & 2)
            ['package_id' => $pkgMiniTiu, 'item_type' => 'tryout', 'item_id' => $tryoutIds[2], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgMiniTiu, 'item_type' => 'material', 'item_id' => $mat1, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgMiniTiu, 'item_type' => 'material', 'item_id' => $mat2, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],

            // Isi Paket VIP Private 1-on-1 (Mentor + Tryout + Materi)
            ['package_id' => $pkgPrivate, 'item_type' => 'private_1on1', 'item_id' => $mentorStisId, 'custom_note' => '4x Sesi Live Mentoring 1-on-1 Matematika STIS & TIU', 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPrivate, 'item_type' => 'private_1on1', 'item_id' => $mentorPsiId, 'custom_note' => 'Konsultasi Personal Evaluasi Psikotes & Manajemen Waktu', 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPrivate, 'item_type' => 'tryout', 'item_id' => $tryoutIds[0], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPrivate, 'item_type' => 'tryout', 'item_id' => $tryoutIds[3], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPrivate, 'item_type' => 'tryout', 'item_id' => $tryoutIds[5], 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPrivate, 'item_type' => 'material', 'item_id' => $mat1, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
            ['package_id' => $pkgPrivate, 'item_type' => 'material', 'item_id' => $mat3, 'custom_note' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // =====================================================================
        // 8. SEED TRANSAKSI, AKSES, RIWAYAT TRYOUT & DIAGNOSTIK PESERTA SEEDER
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

        $attAndi1 = DB::table('tryout_attempts')->insertGetId([
            'user_id' => $studentAndiId,
            'tryout_id' => $tryoutIds[0],
            'started_at' => $now->copy()->subDays(7),
            'finished_at' => $now->copy()->subDays(7)->addMinutes(40),
            'total_score' => 85,
            'is_passed' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        foreach ($firstTryoutSampleQuestions as $idx => $sq) {
            $isCorrect = ($idx % 2 === 0);
            DB::table('tryout_answers')->insert([
                'tryout_attempt_id'  => $attAndi1,
                'question_id'        => $sq['question_id'],
                'question_option_id' => $isCorrect ? $sq['correct_opt'] : $sq['wrong_opt'],
                'score_earned'       => $isCorrect ? 5 : 0,
                'time_spent_seconds' => $isCorrect ? 38 : 95,
                'visit_count'        => $isCorrect ? 1 : 3,
                'is_doubtful'        => !$isCorrect,
                'first_opened_at'    => $now,
                'last_answered_at'   => $now,
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
        }

        DB::table('competency_diagnostics')->insert([
            ['user_id' => $studentAndiId, 'topic_id' => $topicDeretId, 'total_answered' => 20, 'total_correct' => 18, 'accuracy_percentage' => 90.0, 'total_time_seconds' => 760, 'avg_time_seconds' => 38.0, 'status_label' => 'Unggul', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $studentAndiId, 'topic_id' => $topicDenahPsiId, 'total_answered' => 20, 'total_correct' => 17, 'accuracy_percentage' => 85.0, 'total_time_seconds' => 600, 'avg_time_seconds' => 30.0, 'status_label' => 'Unggul', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $studentAndiId, 'topic_id' => $topicPelayananId, 'total_answered' => 20, 'total_correct' => 13, 'accuracy_percentage' => 65.0, 'total_time_seconds' => 960, 'avg_time_seconds' => 48.0, 'status_label' => 'Cukup', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $studentAndiId, 'topic_id' => $topicSilogismeId, 'total_answered' => 20, 'total_correct' => 7, 'accuracy_percentage' => 35.0, 'total_time_seconds' => 1760, 'avg_time_seconds' => 88.0, 'status_label' => 'Lemah', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $studentAndiId, 'topic_id' => $topicPeluangStisId, 'total_answered' => 20, 'total_correct' => 8, 'accuracy_percentage' => 40.0, 'total_time_seconds' => 1900, 'avg_time_seconds' => 95.0, 'status_label' => 'Lemah', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
