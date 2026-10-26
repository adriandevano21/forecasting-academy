-- ============================================================================
-- DATABASE INITIALIZATION: FORECASTING ACADEMY
-- ============================================================================
DROP DATABASE IF EXISTS `forecasting_academy`;

CREATE DATABASE `forecasting_academy` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `forecasting_academy`;

-- ============================================================================
-- BAGIAN 1: PEMBUATAN STRUKTUR TABEL (DDL)
-- ============================================================================

-- 1. Tabel Users & Sessions
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `google_id` VARCHAR(255) NULL UNIQUE,
    `avatar` VARCHAR(255) NULL,
    `password` VARCHAR(255) NULL,
    `role` ENUM(
        'student',
        'admin',
        'instructor'
    ) NOT NULL DEFAULT 'student',
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB;

CREATE TABLE `password_reset_tokens` (
    `email` VARCHAR(255) PRIMARY KEY,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL
) ENGINE = InnoDB;

CREATE TABLE `sessions` (
    `id` VARCHAR(255) PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    INDEX `sessions_user_id_index` (`user_id`),
    INDEX `sessions_last_activity_index` (`last_activity`)
) ENGINE = InnoDB;

-- 2. Tabel Programs
CREATE TABLE `programs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- 3. Tabel Subjects
CREATE TABLE `subjects` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `program_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `passing_grade` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_subjects_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- 4. Tabel Topics
CREATE TABLE `topics` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `subject_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_topics_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- 5. Tabel Materials
CREATE TABLE `materials` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `topic_id` BIGINT UNSIGNED NOT NULL,
    `instructor_id` BIGINT UNSIGNED NULL,
    `title` VARCHAR(255) NOT NULL,
    `type` ENUM('video', 'pdf', 'text') NOT NULL,
    `content_url` VARCHAR(255) NOT NULL,
    `duration` INT NOT NULL DEFAULT 0 COMMENT 'Durasi materi dalam detik',
    `is_free_preview` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_materials_topic` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_materials_instructor` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB;

-- 6. Tabel User Material Progress
CREATE TABLE `user_material_progress` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `material_id` BIGINT UNSIGNED NOT NULL,
    `is_completed` TINYINT(1) NOT NULL DEFAULT 0,
    `last_watched_second` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_ump_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ump_material` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- 7. Tabel Tryouts
CREATE TABLE `tryouts` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `type` ENUM(
        'per_materi',
        'full_skd',
        'psikologi_koran',
        'hafal_denah'
    ) NOT NULL,
    `duration_minutes` INT NOT NULL,
    `total_questions` INT NOT NULL,
    `instruction` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- 8. Tabel Questions (Dengan ideal_time_seconds)
CREATE TABLE `questions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `tryout_id` BIGINT UNSIGNED NOT NULL,
    `topic_id` BIGINT UNSIGNED NOT NULL,
    `question_text` LONGTEXT NOT NULL,
    `question_image` VARCHAR(255) NULL,
    `ideal_time_seconds` INT NOT NULL DEFAULT 54 COMMENT 'Standar waktu ideal pengerjaan soal dalam detik',
    `discussion_text` LONGTEXT NULL,
    `discussion_video_url` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_questions_tryout` FOREIGN KEY (`tryout_id`) REFERENCES `tryouts` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_questions_topic` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- 9. Tabel Question Options
CREATE TABLE `question_options` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `question_id` BIGINT UNSIGNED NOT NULL,
    `option_label` CHAR(2) NOT NULL,
    `option_text` TEXT NOT NULL,
    `score_weight` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_qoptions_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- 10. Tabel Packages
CREATE TABLE `packages` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `type` ENUM(
        'to_materi',
        'to_full',
        'bundling',
        'custom',
        'private_1on1'
    ) NOT NULL,
    `price` BIGINT UNSIGNED NOT NULL,
    `discount_price` BIGINT UNSIGNED NULL,
    `active_period_days` INT NOT NULL DEFAULT 365,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- 11. Tabel Package Items (Polymorphic Pivot)
CREATE TABLE `package_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `package_id` BIGINT UNSIGNED NOT NULL,
    `item_type` VARCHAR(255) NOT NULL,
    `item_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_polymorphic_item` (`item_type`, `item_id`),
    CONSTRAINT `fk_pitems_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- 12. Tabel Transactions
CREATE TABLE `transactions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `package_id` BIGINT UNSIGNED NOT NULL,
    `invoice_code` VARCHAR(255) NOT NULL UNIQUE,
    `total_amount` BIGINT UNSIGNED NOT NULL,
    `payment_status` ENUM('pending', 'paid', 'expired') NOT NULL DEFAULT 'pending',
    `payment_method` VARCHAR(255) NULL,
    `paid_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_trans_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_trans_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- 13. Tabel User Accesses
CREATE TABLE `user_accesses` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `package_id` BIGINT UNSIGNED NOT NULL,
    `expires_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_uaccess_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_uaccess_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- 14. Tabel Tryout Attempts
CREATE TABLE `tryout_attempts` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `tryout_id` BIGINT UNSIGNED NOT NULL,
    `started_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `finished_at` TIMESTAMP NULL,
    `total_score` INT NOT NULL DEFAULT 0,
    `is_passed` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_tattempts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tattempts_tryout` FOREIGN KEY (`tryout_id`) REFERENCES `tryouts` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- 15. Tabel Tryout Answers (Dengan Pelacakan Waktu per Soal)
CREATE TABLE `tryout_answers` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `tryout_attempt_id` BIGINT UNSIGNED NOT NULL,
    `question_id` BIGINT UNSIGNED NOT NULL,
    `question_option_id` BIGINT UNSIGNED NULL,
    `score_earned` INT NOT NULL DEFAULT 0,
    `time_spent_seconds` INT NOT NULL DEFAULT 0 COMMENT 'Akumulasi waktu pengerjaan soal dalam detik',
    `visit_count` INT NOT NULL DEFAULT 0 COMMENT 'Berapa kali soal dibuka/dilihat oleh peserta',
    `is_doubtful` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Penanda tombol Ragu-ragu',
    `first_opened_at` TIMESTAMP NULL,
    `last_answered_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_attempt_question` (
        `tryout_attempt_id`,
        `question_id`
    ),
    CONSTRAINT `fk_tanswers_attempt` FOREIGN KEY (`tryout_attempt_id`) REFERENCES `tryout_attempts` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tanswers_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tanswers_option` FOREIGN KEY (`question_option_id`) REFERENCES `question_options` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB;

-- 16. Tabel Competency Diagnostics (Dengan Analisis Kecepatan Waktu per Topik)
CREATE TABLE `competency_diagnostics` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `topic_id` BIGINT UNSIGNED NOT NULL,
    `total_answered` INT NOT NULL DEFAULT 0,
    `total_correct` INT NOT NULL DEFAULT 0,
    `accuracy_percentage` FLOAT NOT NULL DEFAULT 0,
    `total_time_seconds` INT NOT NULL DEFAULT 0 COMMENT 'Total waktu menjawab seluruh soal di topik ini',
    `avg_time_seconds` FLOAT NOT NULL DEFAULT 0 COMMENT 'Rata-rata detik per soal di topik ini',
    `status_label` ENUM('Unggul', 'Cukup', 'Lemah') NOT NULL DEFAULT 'Lemah',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_cdiag_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cdiag_topic` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- ============================================================================
-- BAGIAN 2: INSERT DATA AWAL (SEEDER SQL)
-- ============================================================================

-- 1. Insert Users (Password default admin & instructor: password123)
INSERT INTO
    `users` (
        `id`,
        `name`,
        `email`,
        `google_id`,
        `avatar`,
        `password`,
        `role`
    )
VALUES (
        1,
        'Admin Forecasting Academy',
        'admin@forecasting-academy.test',
        NULL,
        NULL,
        '$2y$12$K8Y1Z5x9Q8m1W2e3R4t5Y.u7I8o9P0a1S2d3F4g5H6j7K8l9Z0x1C',
        'admin'
    ),
    (
        2,
        'Budi Santoso, M.Stat (Mentor STIS)',
        'budi.mentor@gmail.com',
        '10928374651029384756',
        'https://lh3.googleusercontent.com/a/default-instructor',
        '$2y$12$K8Y1Z5x9Q8m1W2e3R4t5Y.u7I8o9P0a1S2d3F4g5H6j7K8l9Z0x1C',
        'instructor'
    ),
    (
        3,
        'Andi Pratama',
        'andi.student@gmail.com',
        '98765432101234567890',
        'https://lh3.googleusercontent.com/a/default-student',
        NULL,
        'student'
    );

-- 2. Insert Programs
INSERT INTO
    `programs` (
        `id`,
        `name`,
        `slug`,
        `description`
    )
VALUES (
        1,
        'Seleksi Kompetensi Dasar (SKD)',
        'skd-cpns-kedinasan',
        'Program persiapan intensif TWK, TIU, dan TKP untuk CPNS & Sekolah Kedinasan.'
    ),
    (
        2,
        'Kedinasan Politeknik Statistika STIS',
        'kedinasan-stis',
        'Fokus pendalaman Matematika Lanjutan dan SPMB STIS.'
    ),
    (
        3,
        'Tes Psikologi & Ketahanan Kerja',
        'tes-psikologi',
        'Simulasi Tes Koran (Pauli/Kraepelin), Ganjil Genap, dan Hafal Denah.'
    );

-- 3. Insert Subjects
INSERT INTO
    `subjects` (
        `id`,
        `program_id`,
        `name`,
        `passing_grade`
    )
VALUES (
        1,
        1,
        'Tes Wawasan Kebangsaan (TWK)',
        65
    ),
    (
        2,
        1,
        'Tes Intelegensia Umum (TIU)',
        80
    ),
    (
        3,
        1,
        'Tes Karakteristik Pribadi (TKP)',
        166
    ),
    (
        4,
        2,
        'Matematika SPMB STIS',
        75
    );

-- 4. Insert Topics
INSERT INTO
    `topics` (
        `id`,
        `subject_id`,
        `name`,
        `description`
    )
VALUES (
        1,
        1,
        'Bela Negara & Nasionalisme',
        'Pemahaman implementasi sikap bela negara dalam kehidupan sehari-hari.'
    ),
    (
        2,
        2,
        'Penalaran Logis (Silogisme)',
        'Penarikan kesimpulan dari premis mayor dan minor.'
    ),
    (
        3,
        2,
        'Numerik - Deret Angka',
        'Pola barisan bilangan aritmatika dan geometri bertingkat.'
    ),
    (
        4,
        3,
        'Pelayanan Publik',
        'Orientasi keramahtamahan dan solusi cepat terhadap masyarakat.'
    ),
    (
        5,
        4,
        'Peluang & Statistika Dasar',
        'Distribusi peluang, permutasi, kombinasi, dan statistika deskriptif.'
    );

-- 5. Insert Materials & Progress
INSERT INTO
    `materials` (
        `id`,
        `topic_id`,
        `instructor_id`,
        `title`,
        `type`,
        `content_url`,
        `duration`,
        `is_free_preview`
    )
VALUES (
        1,
        2,
        2,
        'Trik Cepat Taklukkan Silogisme & Ekuivalensi dalam 10 Detik',
        'video',
        'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        900,
        1
    ),
    (
        2,
        3,
        2,
        'Pola Rahasia Deret Angka TIU SKD & STIS',
        'video',
        'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        1200,
        0
    );

INSERT INTO
    `user_material_progress` (
        `id`,
        `user_id`,
        `material_id`,
        `is_completed`,
        `last_watched_second`
    )
VALUES (1, 3, 2, 1, 1200);

-- 6. Insert Tryouts
INSERT INTO
    `tryouts` (
        `id`,
        `title`,
        `type`,
        `duration_minutes`,
        `total_questions`,
        `instruction`
    )
VALUES (
        1,
        'Tryout Akbar SKD Kedinasan STIS #1',
        'full_skd',
        100,
        110,
        'Kerjakan dengan jujur. Waktu 100 menit. TIU & TWK Benar +5, Salah 0. TKP rentang skor 1 s.d 5.'
    ),
    (
        2,
        'Mini TO Spesialisasi: Logika & Numerik TIU',
        'per_materi',
        30,
        25,
        'Fokus evaluasi pemetaan kompetensi (Diagnostic) untuk sub-tes TIU.'
    );

-- 7. Insert Questions
INSERT INTO
    `questions` (
        `id`,
        `tryout_id`,
        `topic_id`,
        `question_text`,
        `question_image`,
        `ideal_time_seconds`,
        `discussion_text`,
        `discussion_video_url`
    )
VALUES (
        1,
        1,
        3,
        'Perhatikan deret angka berikut: 3, 7, 15, 31, 63, ... Angka selanjutnya adalah?',
        NULL,
        45,
        'Pola deret adalah dikali 2 lalu ditambah 1 (x2 + 1) atau ditambah 4, 8, 16, 32, 64. Maka 63 + 64 = 127.',
        'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
    ),
    (
        2,
        1,
        2,
        'Semua mahasiswa STIS pandai matematika. Sebagian mahasiswa yang pandai matematika suka membaca buku statistik. Kesimpulan yang tepat adalah...',
        NULL,
        60,
        'Karena premis kedua menggunakan kuantor "Sebagian", maka kesimpulan sah adalah sebagian mahasiswa STIS pandai matematika dan suka membaca buku statistik.',
        NULL
    ),
    (
        3,
        1,
        4,
        'Saat Anda sedang bertugas di loket pelayanan data statistik, datang seorang warga yang marah-marah karena merasa prosedur permohonan data terlalu rumit. Sikap Anda adalah...',
        NULL,
        50,
        'Dalam TKP Pelayanan Publik, pilih opsi yang menunjukkan ketenangan, empati, mendengarkan keluhan, dan langsung memberikan solusi sesuai SOP.',
        NULL
    );

-- 8. Insert Question Options
INSERT INTO
    `question_options` (
        `id`,
        `question_id`,
        `option_label`,
        `option_text`,
        `score_weight`
    )
VALUES
    -- Opsi Soal 1 (Deret Angka - Kunci: C / ID 3)
    (1, 1, 'A', '115', 0),
    (2, 1, 'B', '125', 0),
    (3, 1, 'C', '127', 5),
    (4, 1, 'D', '129', 0),
    (5, 1, 'E', '131', 0),
    -- Opsi Soal 2 (Silogisme - Kunci: B / ID 7)
    (
        6,
        2,
        'A',
        'Semua mahasiswa STIS suka membaca buku statistik.',
        0
    ),
    (
        7,
        2,
        'B',
        'Sebagian mahasiswa STIS pandai matematika dan suka membaca buku statistik.',
        5
    ),
    (
        8,
        2,
        'C',
        'Mahasiswa yang suka statistik pasti mahasiswa STIS.',
        0
    ),
    (
        9,
        2,
        'D',
        'Tidak ada kesimpulan yang benar.',
        0
    ),
    (
        10,
        2,
        'E',
        'Semua yang suka membaca buku statistik adalah mahasiswa STIS.',
        0
    ),
    -- Opsi Soal 3 (TKP Pelayanan Publik - Skor 5 di B / ID 12)
    (
        11,
        3,
        'A',
        'Meminta petugas keamanan untuk menenangkan warga tersebut.',
        2
    ),
    (
        12,
        3,
        'B',
        'Mendengarkan dengan tenang, meminta maaf atas ketidaknyamanannya, lalu menjelaskan alur SOP secara perlahan dan membantu pengisian formulir.',
        5
    ),
    (
        13,
        3,
        'C',
        'Menjelaskan bahwa aturan tersebut sudah ditetapkan pimpinan dan tidak bisa diubah.',
        3
    ),
    (
        14,
        3,
        'D',
        'Tetap tersenyum dan membiarkannya berbicara sampai lelah sendiri.',
        1
    ),
    (
        15,
        3,
        'E',
        'Memanggil atasan agar masalah cepat selesai.',
        4
    );

-- 9. Insert Packages & Package Items
INSERT INTO
    `packages` (
        `id`,
        `name`,
        `slug`,
        `type`,
        `price`,
        `discount_price`,
        `active_period_days`
    )
VALUES (
        1,
        'Paket Bundling Intensif SKD & STIS 2026 (Video + Tryout)',
        'bundling-intensif-skd-stis-2026',
        'bundling',
        250000,
        149000,
        365
    );

INSERT INTO
    `package_items` (
        `id`,
        `package_id`,
        `item_type`,
        `item_id`
    )
VALUES (
        1,
        1,
        'App\\Models\\Material',
        1
    ),
    (
        2,
        1,
        'App\\Models\\Material',
        2
    ),
    (
        3,
        1,
        'App\\Models\\Tryout',
        1
    );

-- 10. Insert Transactions & User Accesses
INSERT INTO
    `transactions` (
        `id`,
        `user_id`,
        `package_id`,
        `invoice_code`,
        `total_amount`,
        `payment_status`,
        `payment_method`,
        `paid_at`
    )
VALUES (
        1,
        3,
        1,
        'INV-FA-20261025-001',
        149000,
        'paid',
        'QRIS',
        NOW()
    );

INSERT INTO
    `user_accesses` (
        `id`,
        `user_id`,
        `package_id`,
        `expires_at`
    )
VALUES (
        1,
        3,
        1,
        DATE_ADD(NOW(), INTERVAL 365 DAY)
    );

-- 11. Insert Tryout Attempts, Answers (dengan Timer per Soal), & Competency Diagnostics
INSERT INTO
    `tryout_attempts` (
        `id`,
        `user_id`,
        `tryout_id`,
        `started_at`,
        `finished_at`,
        `total_score`,
        `is_passed`
    )
VALUES (
        1,
        3,
        1,
        DATE_SUB(NOW(), INTERVAL 85 MINUTE),
        DATE_SUB(NOW(), INTERVAL 5 MINUTE),
        10,
        0
    );

INSERT INTO
    `tryout_answers` (
        `id`,
        `tryout_attempt_id`,
        `question_id`,
        `question_option_id`,
        `score_earned`,
        `time_spent_seconds`,
        `visit_count`,
        `is_doubtful`,
        `first_opened_at`,
        `last_answered_at`
    )
VALUES (
        1,
        1,
        1,
        3,
        5,
        38,
        1,
        0,
        DATE_SUB(NOW(), INTERVAL 84 MINUTE),
        DATE_ADD(
            DATE_SUB(NOW(), INTERVAL 84 MINUTE),
            INTERVAL 38 SECOND
        )
    ),
    (
        2,
        1,
        2,
        6,
        0,
        125,
        3,
        1,
        DATE_SUB(NOW(), INTERVAL 83 MINUTE),
        DATE_SUB(NOW(), INTERVAL 20 MINUTE)
    ),
    (
        3,
        1,
        3,
        12,
        5,
        48,
        1,
        0,
        DATE_SUB(NOW(), INTERVAL 80 MINUTE),
        DATE_ADD(
            DATE_SUB(NOW(), INTERVAL 80 MINUTE),
            INTERVAL 48 SECOND
        )
    );

INSERT INTO
    `competency_diagnostics` (
        `id`,
        `user_id`,
        `topic_id`,
        `total_answered`,
        `total_correct`,
        `accuracy_percentage`,
        `total_time_seconds`,
        `avg_time_seconds`,
        `status_label`
    )
VALUES (
        1,
        3,
        3,
        10,
        9,
        90.0,
        420,
        42.0,
        'Unggul'
    ),
    (
        2,
        3,
        4,
        10,
        6,
        60.0,
        550,
        55.0,
        'Cukup'
    ),
    (
        3,
        3,
        2,
        10,
        3,
        30.0,
        980,
        98.0,
        'Lemah'
    );