<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Users & Sessions
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('google_id')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->string('password')->nullable();
            $table->enum('role', ['student', 'admin', 'instructor'])->default('student');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // 2. Kelompok Tabel CMS Beranda, Menu, Fitur & Footer
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('group')->default('general');
            $table->string('label');
            $table->enum('type', ['text', 'json', 'url'])->default('text');
            $table->text('value')->nullable();
            $table->boolean('is_displayed')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('landing_menus', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url');
            $table->enum('location', ['navbar', 'footer_quicklink'])->default('navbar');
            $table->boolean('is_displayed')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('landing_features', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('icon_key')->default('chart');
            $table->string('color_theme')->default('blue');
            $table->boolean('is_displayed')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Kelompok Tabel Kurikulum & Spesialisasi
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('badge_text')->default('Program Prioritas')->nullable();
            $table->text('description')->nullable();
            $table->string('custom_stat_label')->nullable();
            $table->boolean('show_on_homepage')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->string('name');
            $table->integer('passing_grade')->default(0);
            $table->boolean('is_displayed')->default(true);
            $table->timestamps();
        });

        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 4. Kelompok Tabel Materi Pembelajaran (Video & Modul)
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('topics')->cascadeOnDelete();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->enum('type', ['video', 'pdf', 'text']);
            $table->string('content_url');
            $table->integer('duration')->default(0)->comment('Durasi dalam detik');
            $table->boolean('is_free_preview')->default(false);
            $table->timestamps();
        });

        Schema::create('user_material_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->integer('last_watched_second')->default(0);
            $table->timestamps();
        });

        // 5. Kelompok Master Paket Tryout & Bank Soal (SEJALAN & TERSTRUKTUR)
        Schema::create('tryouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->string('title');
            $table->string('code')->unique()->nullable();
            $table->enum('type', ['per_materi', 'full_skd', 'psikologi_koran', 'hafal_denah']);
            $table->integer('duration_minutes')->default(60);
            $table->integer('total_questions')->default(20);
            $table->text('instruction')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tryout_id')->constrained('tryouts')->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained('topics')->cascadeOnDelete();
            $table->integer('question_number')->default(1)->comment('Nomor urut soal dalam paket tryout');
            $table->longText('question_text');
            $table->string('question_image')->nullable()->comment('Link gambar soal (opsional)');
            $table->integer('ideal_time_seconds')->default(54);
            $table->longText('discussion_text')->nullable()->comment('Pembahasan teks lengkap');
            $table->string('discussion_image')->nullable()->comment('Link gambar pembahasan (opsional)');
            $table->string('discussion_video_url')->nullable()->comment('Link video pembahasan (opsional)');
            $table->timestamps();
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->char('option_label', 2); // A, B, C, D, E
            $table->text('option_text');
            $table->string('option_image')->nullable();
            $table->integer('score_weight')->default(0);
            $table->timestamps();
        });

        // 6. Kelompok Paket Penjualan & Isi Bundling (Diambil dari Tryout, Materi & Private 1-on-1)
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['to_materi', 'to_full', 'bundling', 'custom', 'private_1on1']);
            $table->string('badge_text')->nullable();
            $table->text('short_description')->nullable();
            $table->json('features')->nullable();
            $table->integer('private_sessions_count')->default(0)->comment('Kuota sesi mentoring Private 1-on-1 jika ada');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('discount_price')->nullable();
            $table->integer('active_period_days')->default(365);
            $table->boolean('is_featured')->default(false);
            $table->boolean('show_on_homepage')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->enum('item_type', ['tryout', 'material', 'private_1on1']);
            $table->unsignedBigInteger('item_id')->default(0)->comment('ID dari tabel tryouts, materials, atau mentor');
            $table->string('custom_note')->nullable();
            $table->index(['item_type', 'item_id']);
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('invoice_code')->unique();
            $table->unsignedBigInteger('total_amount');
            $table->enum('payment_status', ['pending', 'paid', 'expired'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // 7. Kelompok Sesi Ujian, Jawaban per Soal & Competency Diagnostic
        Schema::create('tryout_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tryout_id')->constrained('tryouts')->cascadeOnDelete();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('finished_at')->nullable();
            $table->integer('total_score')->default(0);
            $table->boolean('is_passed')->default(false);
            $table->timestamps();
        });

        Schema::create('tryout_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tryout_attempt_id')->constrained('tryout_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->foreignId('question_option_id')->nullable()->constrained('question_options')->nullOnDelete();
            $table->integer('score_earned')->default(0);
            $table->integer('time_spent_seconds')->default(0);
            $table->integer('visit_count')->default(0);
            $table->boolean('is_doubtful')->default(false);
            $table->timestamp('first_opened_at')->nullable();
            $table->timestamp('last_answered_at')->nullable();
            $table->timestamps();
            $table->unique(['tryout_attempt_id', 'question_id'], 'uq_attempt_question');
        });

        Schema::create('competency_diagnostics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained('topics')->cascadeOnDelete();
            $table->integer('total_answered')->default(0);
            $table->integer('total_correct')->default(0);
            $table->float('accuracy_percentage')->default(0);
            $table->integer('total_time_seconds')->default(0);
            $table->float('avg_time_seconds')->default(0);
            $table->enum('status_label', ['Unggul', 'Cukup', 'Lemah'])->default('Lemah');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competency_diagnostics');
        Schema::dropIfExists('tryout_answers');
        Schema::dropIfExists('tryout_attempts');
        Schema::dropIfExists('user_accesses');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('package_items');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('tryouts');
        Schema::dropIfExists('user_material_progress');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('topics');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('landing_features');
        Schema::dropIfExists('landing_menus');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};