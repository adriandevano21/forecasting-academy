<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CatExamController;

// Halaman Landing Page (Beranda)
Route::get('/', [BerandaController::class, 'index'])->name('beranda');

// Rute Autentikasi (Email/Password & Google OAuth)
Route::get('/login', [GoogleAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [GoogleAuthController::class, 'loginWithPassword'])->name('login.post');
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('login.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('login.google.callback');
Route::post('/logout', [GoogleAuthController::class, 'logout'])->name('logout');

// Rute Terproteksi Login
Route::middleware(['auth'])->group(function () {

    // Dashboard Siswa & Mentor
    Route::get('/student/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/student/dashboard/password', [DashboardController::class, 'updatePassword'])->name('dashboard.password.update');

    // =========================================================================
    // PANEL KHUSUS ADMIN (Mengatur CMS Beranda & Seluruh 16 Tabel Database)
    // =========================================================================
    Route::prefix('control-admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        // Saklar Cepat (Toggle On/Off) Tampil di Beranda / Status Lunas
        Route::post('/toggle/{table}/{id}/{column}', [AdminController::class, 'toggleBoolean'])->name('toggle');

        // 1. CMS Beranda (site_settings, landing_menus, landing_features)
        Route::post('/site-settings/bulk', [AdminController::class, 'updateSiteSettingsBulk'])->name('settings.bulk');
        Route::post('/site-settings/store', [AdminController::class, 'storeSiteSetting'])->name('settings.store');
        Route::post('/landing-menus/save', [AdminController::class, 'saveLandingMenu'])->name('menus.save');
        Route::post('/landing-features/save', [AdminController::class, 'saveLandingFeature'])->name('features.save');

        // 2. Kurikulum (programs, subjects, topics)
        Route::post('/programs/save', [AdminController::class, 'saveProgram'])->name('programs.save');
        Route::post('/subjects/save', [AdminController::class, 'saveSubject'])->name('subjects.save');
        Route::post('/topics/save', [AdminController::class, 'saveTopic'])->name('topics.save');

        // 3. Materi Pembelajaran (materials)
        Route::post('/materials/save', [AdminController::class, 'saveMaterial'])->name('materials.save');

        // 4. Tryout, Bank Soal & Opsi Jawaban A-E (tryouts, questions, question_options)
        Route::post('/tryouts/save', [AdminController::class, 'saveTryout'])->name('tryouts.save');
        Route::post('/questions/save', [AdminController::class, 'saveQuestionWithOptions'])->name('questions.save');

        // 5. Paket Belajar & Isi Paket (packages, package_items)
        Route::post('/packages/save', [AdminController::class, 'savePackage'])->name('packages.save');
        Route::post('/package-items/save', [AdminController::class, 'savePackageItem'])->name('package_items.save');

        // 6. Pengguna, Transaksi & Hak Akses (users, transactions, user_accesses)
        Route::post('/users/save', [AdminController::class, 'saveUser'])->name('users.save');
        Route::post('/transactions/save', [AdminController::class, 'saveTransaction'])->name('transactions.save');
        Route::post('/user-accesses/save', [AdminController::class, 'saveUserAccess'])->name('accesses.save');

        // 7. Evaluasi & Diagnostik (competency_diagnostics)
        Route::post('/diagnostics/save', [AdminController::class, 'saveDiagnostic'])->name('diagnostics.save');

        // 8. Hapus Data Universal untuk Tabel Mana Saja
        Route::delete('/delete/{table}/{id}', [AdminController::class, 'destroyRecord'])->name('delete');
    });

    // =========================================================================
    // ROUTE MESIN UJIAN CAT (COMPUTER ASSISTED TEST) PESERTA
    // =========================================================================
    Route::prefix('dashboard/tryout')->name('cat.')->group(function () {
        // 1. Halaman Penjelasan Tryout & Tampilan Ujian CAT
        Route::get('/{id}/exam', [CatExamController::class, 'show'])->name('show');

        // 2. Endpoint AJAX Konfirmasi Mulai Ujian (Generate Soal & Mulai Timer)
        Route::post('/{id}/start', [CatExamController::class, 'startExam'])->name('start');

        // 3. Endpoint AJAX Simpan Jawaban & Akumulasi Waktu per Soal (time_spent_seconds)
        Route::post('/attempt/{attemptId}/answer', [CatExamController::class, 'saveAnswer'])->name('answer');

        // 4. Endpoint AJAX Selesai Ujian & Hitung Competency Diagnostics
        Route::post('/attempt/{attemptId}/finish', [CatExamController::class, 'finishExam'])->name('finish');
    });
});
