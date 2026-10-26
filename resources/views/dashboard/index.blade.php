<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Dashboard -
        {{ ($settings['brand_name'] ?? 'Forecasting') . ' ' . ($settings['brand_subname'] ?? 'Academy') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('dashboard/css/dashboard.css') }}">
</head>

<body>

    <div class="cursor-dot" id="cursor-dot"></div>
    <div class="cursor-outline" id="cursor-outline"></div>

    <div class="animated-bg">
        <canvas id="bg-canvas"></canvas>
        <div class="bg-blob blob-1"></div>
        <div class="bg-blob blob-2"></div>
    </div>

    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <div class="dashboard-wrapper">
        <!-- SIDEBAR COMPACT -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('beranda') }}" class="brand-logo">
                    <div class="logo-icon">FA</div>
                    <div class="brand-text">
                        <span class="brand-title">{{ $settings['brand_name'] ?? 'Forecasting' }}</span>
                        <span class="brand-subtitle">{{ $settings['brand_subname'] ?? 'Academy' }}</span>
                    </div>
                </a>
                <button type="button" class="sidebar-close-btn" id="sidebar-close">&times;</button>
            </div>

            <div class="sidebar-user-card">
                <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=2563EB&color=fff&bold=true' }}"
                    alt="Avatar" class="user-avatar">
                <div class="user-meta">
                    <h4 class="user-name">{{ $user->name }}</h4>
                    <span class="user-role-badge">
                        <span class="dot-online"></span>
                        {{ strtoupper($user->role ?? 'STUDENT') }}
                    </span>
                </div>
            </div>

            <div class="sidebar-menu-label">MENU DASHBOARD</div>
            <nav class="sidebar-nav">
                <button type="button" class="nav-item active" data-tab="tab-overview">
                    <span>📊 Ringkasan & Progres</span>
                </button>
                <button type="button" class="nav-item" data-tab="tab-diagnostic">
                    <span>🎯 Competency Diagnostic</span>
                    @if ($diagSummary['lemah'] > 0)
                        <span class="nav-badge badge-danger">{{ $diagSummary['lemah'] }}</span>
                    @endif
                </button>
                <button type="button" class="nav-item" data-tab="tab-tryout">
                    <span>📝 Simulasi Tryout (CBT)</span>
                    <span class="nav-badge badge-blue">{{ $tryouts->count() }}</span>
                </button>
                <button type="button" class="nav-item" data-tab="tab-materials">
                    <span>🎬 Video Materi & Modul</span>
                </button>
                <button type="button" class="nav-item" data-tab="tab-psikotes">
                    <span>🧠 Lab Tes Psikologi</span>
                </button>
                <button type="button" class="nav-item" data-tab="tab-packages">
                    <span>🛒 Katalog Paket & Akses</span>
                </button>
                <button type="button" class="nav-item" data-tab="tab-settings">
                    <span>🔐 Pengaturan & Password</span>
                    @if (empty($user->password))
                        <span class="nav-badge badge-amber">Baru</span>
                    @endif
                </button>
            </nav>

            <div class="sidebar-footer">
                <a href="{{ route('beranda') }}" class="btn-back-home">&larr; Ke Halaman Beranda</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">Keluar (Logout)</button>
                </form>
            </div>
        </aside>

        <!-- MAIN AREA -->
        <div class="main-area">
            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" class="hamburger-btn" id="sidebar-open" aria-label="Menu">
                        <span></span><span></span><span></span>
                    </button>
                    <div>
                        <h1 class="page-greeting">Halo, {{ explode(' ', $user->name)[0] }}! 👋</h1>
                        <p class="page-subgreeting">Role: <strong>{{ ucfirst($user->role) }}</strong> •
                            {{ $user->email }}</p>
                    </div>
                </div>

                <div class="topbar-right">
                    @if (empty($user->password))
                        <button type="button" class="quick-stat-pill" data-switch-tab="tab-settings">
                            <span>🔑 Buat Password Login</span>
                        </button>
                    @else
                        <div class="quick-stat-pill" data-switch-tab="tab-diagnostic">
                            <span>⚡ Kecepatan: <strong>{{ $diagSummary['avg_speed'] }} dtk/soal</strong></span>
                        </div>
                    @endif
                </div>
            </header>

            <main class="dashboard-content">
                @if (session('success'))
                    <div class="alert-banner alert-success">
                        <span>✅ {{ session('success') }}</span>
                        <button type="button" class="alert-close"
                            onclick="this.parentElement.remove()">&times;</button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert-banner alert-danger">
                        <span>⚠️ {{ $errors->first() }}</span>
                        <button type="button" class="alert-close"
                            onclick="this.parentElement.remove()">&times;</button>
                    </div>
                @endif

                <!-- Notifikasi Khusus Pengguna Google yang Belum Mengatur Password -->
                @if (empty($user->password))
                    <div class="password-prompt-bar">
                        <div>
                            <strong>💡 Anda login menggunakan akun Google.</strong>
                            <span>Ingin bisa login langsung memakai Email & Password? Atur password Anda
                                sekarang.</span>
                        </div>
                        <button type="button" class="btn-sm-primary" data-switch-tab="tab-settings">Atur
                            Password</button>
                    </div>
                @endif

                <!-- Panel Khusus Jika Login Sebagai ADMIN atau MENTOR -->
                @if ($user->role === 'admin' && $roleStats)
                    <div class="role-special-banner">
                        <h4>🛡️ Panel Ringkasan Administrator</h4>
                        <div class="role-stats-row">
                            <div><small>Total Peserta</small><strong>{{ $roleStats['total_students'] }} Siswa</strong>
                            </div>
                            <div><small>Total Mentor</small><strong>{{ $roleStats['total_mentors'] }} Pengajar</strong>
                            </div>
                            <div><small>Total Sesi Tryout</small><strong>{{ $roleStats['total_attempts'] }}
                                    Sesi</strong></div>
                            <div><small>Total Pendapatan Lunas</small><strong>Rp
                                    {{ number_format($roleStats['total_revenue'], 0, ',', '.') }}</strong></div>
                        </div>
                    </div>
                @elseif($user->role === 'instructor' && $roleStats)
                    <div class="role-special-banner">
                        <h4>👨‍🏫 Panel Ringkasan Mentor / Pengajar</h4>
                        <div class="role-stats-row">
                            <div><small>Video Materi Saya</small><strong>{{ $roleStats['my_materials_count'] }}
                                    Modul/Video</strong></div>
                            <div><small>Total Ditonton Peserta</small><strong>{{ $roleStats['total_views'] }} Kali
                                    Ditonton</strong></div>
                        </div>
                    </div>
                @endif

                <!-- ==========================================================
                     TAB 1: OVERVIEW
                     ========================================================== -->
                <section class="tab-pane active" id="tab-overview">
                    <div class="welcome-banner">
                        <div class="wb-content">
                            <span class="wb-badge">🔥 Smart Analytics CBT</span>
                            <h2>Pantau Pemetaan Sub-Topik & Kecepatan Menjawabmu!</h2>
                            @if ($diagnostics->count() > 0)
                                <p>Anda telah mengerjakan <strong>{{ $attemptsHistory->count() }} sesi tryout</strong>.
                                    Terdapat <strong>{{ $diagSummary['lemah'] }} topik berstatus Lemah</strong> yang
                                    direkomendasikan untuk segera diperbaiki.</p>
                            @else
                                <p>Akun Anda masih baru dan belum memiliki riwayat pengerjaan tryout. Yuk pilih paket
                                    belajar atau mulai simulasi tryout pertama Anda!</p>
                            @endif
                            <div class="wb-actions">
                                <button type="button" class="btn-primary" data-switch-tab="tab-tryout">Mulai
                                    Tryout</button>
                                <button type="button" class="btn-white" data-switch-tab="tab-packages">Lihat Paket
                                    Belajar</button>
                            </div>
                        </div>
                        <div class="wb-visual">
                            <div class="score-circle">
                                <span class="sc-val">{{ $diagSummary['avg_accuracy'] }}%</span>
                                <span class="sc-lbl">Akurasi Rata-rata</span>
                            </div>
                        </div>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="sc-icon icon-blue">📦</div>
                            <div>
                                <span class="sc-title">Paket Aktif Saya</span>
                                <h3 class="sc-value">{{ $myAccesses->where('is_active', true)->count() }}
                                    <small>Paket</small></h3>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="sc-icon icon-emerald">🏆</div>
                            <div>
                                <span class="sc-title">Topik Status Unggul</span>
                                <h3 class="sc-value">{{ $diagSummary['unggul'] }} <small>Topik</small></h3>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="sc-icon icon-amber">⏱️</div>
                            <div>
                                <span class="sc-title">Rata-rata Kecepatan</span>
                                <h3 class="sc-value">{{ $diagSummary['avg_speed'] }} <small>dtk/soal</small></h3>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="sc-icon icon-rose">📝</div>
                            <div>
                                <span class="sc-title">Tryout Dikerjakan</span>
                                <h3 class="sc-value">{{ $attemptsHistory->count() }} <small>Sesi</small></h3>
                            </div>
                        </div>
                    </div>

                    <div class="overview-two-col">
                        <!-- Peta Kompetensi Sub-Topik -->
                        <div class="panel-card">
                            <div class="panel-header">
                                <div>
                                    <h3 class="panel-title">📊 Peta Kompetensi Sub-Topik Anda</h3>
                                    <p class="panel-subtitle">Murni dari hasil pengerjaan Tryout di akun Anda</p>
                                </div>
                                @if ($diagnostics->count() > 0)
                                    <button type="button" class="btn-link" data-switch-tab="tab-diagnostic">Lihat
                                        Detail &rarr;</button>
                                @endif
                            </div>

                            @if ($diagnostics->count() > 0)
                                <div class="diag-list">
                                    @foreach ($diagnostics as $diag)
                                        @php $stClass = strtolower($diag->status_label); @endphp
                                        <div class="diag-row">
                                            <div class="diag-row-top">
                                                <div>
                                                    <span class="subj-tag">{{ $diag->subject_name }}</span>
                                                    <h4 class="diag-topic-title">{{ $diag->topic_name }}</h4>
                                                </div>
                                                <div class="diag-badges">
                                                    <span class="time-pill">⏱️
                                                        {{ number_format($diag->avg_time_seconds, 0) }} dtk</span>
                                                    <span
                                                        class="status-badge badge-{{ $stClass }}">{{ $diag->status_label }}
                                                        ({{ number_format($diag->accuracy_percentage, 0) }}%)</span>
                                                </div>
                                            </div>
                                            <div class="progress-bar-bg">
                                                <div class="progress-bar-fill fill-{{ $stClass }}"
                                                    style="width: {{ $diag->accuracy_percentage }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state-box">
                                    <div class="es-icon">📭</div>
                                    <h4>Belum Ada Data Competency Diagnostic</h4>
                                    <p>Anda belum mengerjakan Tryout apa pun di akun ini. Kerjakan Tryout pertama Anda
                                        untuk melihat pemetaan topik Unggul, Cukup, dan Lemah secara otomatis.</p>
                                    <button type="button" class="btn-primary" data-switch-tab="tab-tryout">Kerjakan
                                        Tryout Sekarang</button>
                                </div>
                            @endif
                        </div>

                        <!-- Kolom Kanan: Paket Aktif & Riwayat Tryout Terakhir -->
                        <div class="side-stack">
                            <div class="panel-card">
                                <div class="panel-header">
                                    <h3 class="panel-title">🎟️ Paket Aktif Saya ({{ $myAccesses->count() }})</h3>
                                    <button type="button" class="btn-link" data-switch-tab="tab-packages">+ Beli
                                        Paket</button>
                                </div>
                                @if ($myAccesses->count() > 0)
                                    <div class="pkg-mini-stack">
                                        @foreach ($myAccesses as $acc)
                                            <div class="my-pkg-mini">
                                                <div class="mpm-top">
                                                    <span
                                                        class="mpm-badge">{{ $acc->badge_text ?? 'Paket Aktif' }}</span>
                                                    <span class="mpm-days">Aktif {{ $acc->days_remaining }}
                                                        Hari</span>
                                                </div>
                                                <h4>{{ $acc->package_name }}</h4>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="empty-state-box compact">
                                        <p>Anda belum memiliki paket belajar aktif di akun ini.</p>
                                        <button type="button" class="btn-primary"
                                            data-switch-tab="tab-packages">Pilih Paket Belajar</button>
                                    </div>
                                @endif
                            </div>

                            <div class="panel-card">
                                <div class="panel-header">
                                    <h3 class="panel-title">🕒 Riwayat Sesi Tryout</h3>
                                </div>
                                @if ($attemptsHistory->count() > 0)
                                    <div class="diag-list">
                                        @foreach ($attemptsHistory->take(3) as $att)
                                            <div class="diag-row">
                                                <div class="diag-row-top" style="margin-bottom:0;">
                                                    <div>
                                                        <h4 class="diag-topic-title">{{ $att->tryout_title }}</h4>
                                                        <small style="color:var(--text-muted); font-size:0.72rem;">Skor
                                                            Akhir: <strong>{{ $att->total_score }}
                                                                Poin</strong></small>
                                                    </div>
                                                    <span
                                                        class="status-badge {{ $att->is_passed ? 'badge-unggul' : 'badge-lemah' }}">
                                                        {{ $att->is_passed ? 'LULUS PG' : 'BELUM LULUS' }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p style="font-size:0.8rem; color:var(--text-secondary);">Belum ada riwayat
                                        pengerjaan tryout.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==========================================================
                     TAB 2: COMPETENCY DIAGNOSTIC & TIME-SPENT LOGS
                     ========================================================== -->
                <section class="tab-pane" id="tab-diagnostic">
                    <div class="section-head-flex">
                        <span class="section-tag">Evaluasi Cerdas Berbasis Data</span>
                        <h2>Rapor Competency Diagnostic & Analisis Waktu per Soal</h2>
                        <p>Memetakan penguasaan Anda di setiap sub-topik menjadi <strong>Unggul (≥75%)</strong>,
                            <strong>Cukup (50–74%)</strong>, dan <strong>Lemah (&lt;50%)</strong>.</p>
                    </div>

                    @if ($diagnostics->count() > 0)
                        <div class="diag-full-grid">
                            @foreach ($diagnostics as $diag)
                                @php $stClass = strtolower($diag->status_label); @endphp
                                <div class="diag-card">
                                    <div>
                                        <div class="dc-header">
                                            <span class="subj-tag">{{ $diag->subject_name }}</span>
                                            <span
                                                class="status-badge badge-{{ $stClass }}">{{ $diag->status_label }}</span>
                                        </div>
                                        <h3 class="dc-title">{{ $diag->topic_name }}</h3>
                                        <p class="dc-desc">{{ $diag->topic_desc }}</p>
                                    </div>
                                    <div>
                                        <div class="dc-metrics">
                                            <div class="dcm-box">
                                                <small>Akurasi Jawaban</small>
                                                <strong>{{ number_format($diag->accuracy_percentage, 0) }}%
                                                    ({{ $diag->total_correct }}/{{ $diag->total_answered }})</strong>
                                            </div>
                                            <div class="dcm-box">
                                                <small>Kecepatan Rata-rata</small>
                                                <strong>{{ number_format($diag->avg_time_seconds, 0) }}
                                                    dtk/soal</strong>
                                            </div>
                                        </div>
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill fill-{{ $stClass }}"
                                                style="width: {{ $diag->accuracy_percentage }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="panel-card mt-18">
                            <div class="panel-header">
                                <div>
                                    <h3 class="panel-title">⏱️ Log Pelacakan Waktu Per Butir Soal</h3>
                                    <p class="panel-subtitle">Mendeteksi soal mana yang membuat Anda overtime atau
                                        bolak-balik ragu</p>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="custom-table">
                                    <thead>
                                        <tr>
                                            <th>Topik</th>
                                            <th>Cuplikan Soal</th>
                                            <th>Waktu Anda</th>
                                            <th>Ideal</th>
                                            <th>Dilihat</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($questionSpeedLogs as $log)
                                            @php $over = $log->time_spent_seconds > $log->ideal_time_seconds; @endphp
                                            <tr>
                                                <td><span class="subj-tag">{{ $log->topic_name }}</span></td>
                                                <td>{{ \Illuminate\Support\Str::limit($log->question_text, 55) }}</td>
                                                <td><strong>{{ $log->time_spent_seconds }} dtk</strong></td>
                                                <td>{{ $log->ideal_time_seconds }} dtk</td>
                                                <td>{{ $log->visit_count }}x</td>
                                                <td>
                                                    <span
                                                        class="status-badge {{ $over ? 'badge-lemah' : 'badge-unggul' }}">
                                                        {{ $over ? 'Overtime' : 'Cepat' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="panel-card">
                            <div class="empty-state-box">
                                <div class="es-icon">📊</div>
                                <h4>Data Diagnostik & Log Waktu Masih Kosong</h4>
                                <p>Akun Anda belum mengerjakan sesi Tryout. Silakan kerjakan Tryout untuk melihat rapor
                                    pemetaan topik dan kecepatan menjawab per butir soal.</p>
                                <button type="button" class="btn-primary" data-switch-tab="tab-tryout">Mulai Tryout
                                    Pertama</button>
                            </div>
                        </div>
                    @endif
                </section>

                <!-- ==========================================================
                     TAB 3: SIMULASI TRYOUT (CBT)
                     ========================================================== -->
                <section class="tab-pane" id="tab-tryout">
                    <div class="section-head-flex">
                        <span class="section-tag">Computer Based Test (CBT)</span>
                        <h2>Daftar Simulasi Tryout SKD, STIS & Psikotes</h2>
                        <p>Dilengkapi penghitung waktu mundur otomatis dan pencatat durasi per butir soal.</p>
                    </div>

                    <div class="tryout-grid">
                        @foreach ($tryouts as $to)
                            <div class="tryout-card">
                                <div>
                                    <div class="tc-top">
                                        <span
                                            class="subj-tag">{{ strtoupper(str_replace('_', ' ', $to->type)) }}</span>
                                        <span class="time-pill">⏱️ {{ $to->duration_minutes }} Menit</span>
                                    </div>
                                    <h3 class="tc-title">{{ $to->title }}</h3>
                                    <p class="tc-inst">{{ $to->instruction }}</p>
                                </div>
                                <div>
                                    <div class="tc-specs">
                                        <div class="tcs-item">
                                            <span>Jumlah Soal</span>
                                            <strong>{{ $to->total_questions }} Butir</strong>
                                        </div>
                                        <div class="tcs-item">
                                            <span>Riwayat Anda</span>
                                            <strong>{{ $to->user_attempts_count > 0 ? $to->user_attempts_count . 'x (Skor: ' . $to->best_score . ')' : 'Belum Pernah' }}</strong>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-primary btn-block trigger-info-modal"
                                        data-title="Mulai CBT: {{ $to->title }}"
                                        data-msg="Sesi ujian '{{ $to->title }}' ({{ $to->duration_minutes }} menit) siap dimulai.">
                                        Mulai Kerjakan
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <!-- ==========================================================
                     TAB 4: VIDEO MATERI & MODUL
                     ========================================================== -->
                <section class="tab-pane" id="tab-materials">
                    <div class="section-head-flex">
                        <span class="section-tag">Learning Management System</span>
                        <h2>Video Pembelajaran & Modul Trik Cepat</h2>
                        <p>Dipandu langsung oleh mentor statistika STIS dan praktisi psikotes.</p>
                    </div>

                    <div class="materials-grid">
                        @foreach ($materials as $mat)
                            <div class="material-card">
                                <div class="mc-video-banner">
                                    <div class="mc-play-icon">▶</div>
                                    <span class="mc-duration">{{ $mat->duration_minutes }} Menit</span>
                                    @if ($mat->is_free_preview)
                                        <span class="mc-free-tag">Gratis Preview</span>
                                    @endif
                                </div>
                                <div class="mc-body">
                                    <span class="subj-tag">{{ $mat->subject_name }}</span>
                                    <h3 class="mc-title">{{ $mat->title }}</h3>
                                    <p class="mc-instructor">👨‍🏫 {{ $mat->instructor_name }}</p>
                                    <div class="mc-progress-wrap">
                                        <div class="mcp-label">
                                            <span>Progres Tontonan</span>
                                            <strong>{{ $mat->progress_percent }}%</strong>
                                        </div>
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill fill-unggul"
                                                style="width: {{ $mat->progress_percent }}%"></div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-outline btn-block trigger-video-modal"
                                        data-title="{{ $mat->title }}">
                                        Putar Video Materi
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <!-- ==========================================================
                     TAB 5: LAB TES PSIKOLOGI
                     ========================================================== -->
                <section class="tab-pane" id="tab-psikotes">
                    <div class="section-head-flex">
                        <span class="section-tag">Simulasi Non-Kognitif</span>
                        <h2>Laboratorium Tes Psikologi (Koran, Ganjil Genap, Hafal Denah)</h2>
                        <p>Latih kecepatan hitung, fokus tahan stres, dan daya ingat visual denah secara interaktif.</p>
                    </div>

                    <div class="psikotes-grid">
                        <div class="psiko-card">
                            <div>
                                <div class="sc-icon icon-blue" style="margin-bottom:10px;">🧮</div>
                                <span class="subj-tag">Ketelitian & Grafik Kerja</span>
                                <h3 class="tc-title">Tes Koran (Pauli / Kraepelin)</h3>
                                <p class="tc-inst">Simulasi penjumlahan deret angka vertikal dengan perpindahan kolom
                                    otomatis untuk mengukur kecepatan dan konsistensi kerja.</p>
                            </div>
                            <button type="button" class="btn-primary btn-block trigger-info-modal"
                                data-title="Simulator Tes Koran"
                                data-msg="Membuka lembar lajur angka Tes Koran interaktif.">Mulai Tes Koran</button>
                        </div>

                        <div class="psiko-card">
                            <div>
                                <div class="sc-icon icon-amber" style="margin-bottom:10px;">⚡</div>
                                <span class="subj-tag">Fokus & Refleks Cepat</span>
                                <h3 class="tc-title">Tes Ganjil Genap Cepat</h3>
                                <p class="tc-inst">Uji refleks kognitif dan akurasi klasifikasi kombinasi angka
                                    ganjil-genap dalam tekanan hitung mundur detik.</p>
                            </div>
                            <button type="button" class="btn-primary btn-block trigger-info-modal"
                                data-title="Simulator Ganjil Genap"
                                data-msg="Membuka simulator kecepatan Ganjil Genap.">Mulai Ganjil Genap</button>
                        </div>

                        <div class="psiko-card">
                            <div>
                                <div class="sc-icon icon-emerald" style="margin-bottom:10px;">🗺️</div>
                                <span class="subj-tag">Memorisasi 2 Fase</span>
                                <h3 class="tc-title">Tes Hafal Denah & Arah</h3>
                                <p class="tc-inst">Fase 1 menghafal letak gedung pada peta, lalu Fase 2 peta
                                    disembunyikan otomatis saat Anda menjawab soal arah lokasi.</p>
                            </div>
                            <button type="button" class="btn-primary btn-block trigger-info-modal"
                                data-title="Simulator 2 Fase: Hafal Denah"
                                data-msg="Fase 1 (Memorisasi Peta Denah) siap dimulai.">Mulai Hafal Denah</button>
                        </div>
                    </div>
                </section>

                <!-- ==========================================================
                     TAB 6: KATALOG PAKET BELAJAR (TERMASUK PSIKOTES)
                     ========================================================== -->
                <section class="tab-pane" id="tab-packages">
                    <div class="section-head-flex">
                        <span class="section-tag">Katalog Lengkap ({{ $catalogPackages->count() }} Pilihan
                            Paket)</span>
                        <h2>Pilih Paket Belajar SKD, STIS & Tes Psikologi</h2>
                        <p>Tersedia paket Bundling Lengkap, Paket Khusus Tes Psikologi, TO Full, Mini TO per Materi,
                            hingga Private 1-on-1.</p>
                    </div>

                    <div class="catalog-grid">
                        @foreach ($catalogPackages as $pkg)
                            <div class="catalog-card {{ $pkg->is_featured ? 'featured-card' : '' }}">
                                <div>
                                    @if ($pkg->is_featured)
                                        <div class="card-ribbon">FAVORIT</div>
                                    @endif
                                    <span class="subj-tag">{{ $pkg->badge_text ?? 'Paket Belajar' }}</span>
                                    <h3 class="pkg-title">{{ $pkg->name }}</h3>
                                    <p class="pkg-desc">{{ $pkg->short_description }}</p>

                                    <div class="pkg-price-box">
                                        @if ($pkg->discount_price)
                                            <span class="price-old">Rp
                                                {{ number_format($pkg->price, 0, ',', '.') }}</span>
                                            <div class="price-now">Rp
                                                {{ number_format($pkg->discount_price, 0, ',', '.') }}</div>
                                        @else
                                            <div class="price-now">Rp {{ number_format($pkg->price, 0, ',', '.') }}
                                            </div>
                                        @endif
                                        <small>Masa aktif {{ $pkg->active_period_days }} Hari</small>
                                    </div>

                                    <ul class="pkg-feat-list">
                                        @foreach ($pkg->features_list as $f)
                                            <li>✔️ {{ $f }}</li>
                                        @endforeach
                                    </ul>
                                </div>

                                @if ($pkg->is_owned)
                                    <button type="button" class="btn-owned" disabled>✅ Sudah Aktif di Akun
                                        Anda</button>
                                @else
                                    <button type="button" class="btn-primary btn-block trigger-info-modal"
                                        data-title="Checkout: {{ $pkg->name }}"
                                        data-msg="Membuka pembayaran QRIS / Virtual Account untuk {{ $pkg->name }}.">
                                        Beli & Aktifkan Paket
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="panel-card mt-18">
                        <div class="panel-header">
                            <h3 class="panel-title">🧾 Riwayat Transaksi Pembelian Saya</h3>
                        </div>
                        @if ($transactions->count() > 0)
                            <div class="table-responsive">
                                <table class="custom-table">
                                    <thead>
                                        <tr>
                                            <th>Invoice</th>
                                            <th>Nama Paket</th>
                                            <th>Metode</th>
                                            <th>Nominal</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $trx)
                                            <tr>
                                                <td><strong>{{ $trx->invoice_code }}</strong></td>
                                                <td>{{ $trx->package_name }}</td>
                                                <td>{{ $trx->payment_method }}</td>
                                                <td>Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                                                <td><span
                                                        class="status-badge badge-unggul">{{ strtoupper($trx->payment_status) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p style="font-size:0.82rem; color:var(--text-secondary);">Belum ada riwayat pembelian
                                paket pada akun ini.</p>
                        @endif
                    </div>
                </section>

                <!-- ==========================================================
                     TAB 7: PENGATURAN AKUN & BUAT/UBAH PASSWORD
                     ========================================================== -->
                <section class="tab-pane" id="tab-settings">
                    <div class="section-head-flex">
                        <span class="section-tag">Keamanan & Kredensial</span>
                        <h2>Pengaturan Akun & Password Login</h2>
                        <p>Atur atau perbarui password Anda agar dapat login menggunakan Email & Password selain
                            menggunakan Google.</p>
                    </div>

                    <div class="overview-two-col">
                        <div class="panel-card">
                            <h3 class="panel-title" style="margin-bottom:6px;">
                                {{ empty($user->password) ? '🔑 Buat Password Baru untuk Akun Google Anda' : '🔐 Perbarui Password Login Anda' }}
                            </h3>
                            <p class="panel-subtitle" style="margin-bottom:16px;">
                                Email Login Anda: <strong>{{ $user->email }}</strong>
                            </p>

                            <form action="{{ route('dashboard.password.update') }}" method="POST">
                                @csrf
                                <div class="form-group-dash">
                                    <label>Password Baru (Minimal 6 Karakter)</label>
                                    <input type="password" name="new_password" class="input-dash"
                                        placeholder="Masukkan password baru" required minlength="6">
                                </div>
                                <div class="form-group-dash">
                                    <label>Konfirmasi Password Baru</label>
                                    <input type="password" name="new_password_confirmation" class="input-dash"
                                        placeholder="Ulangi password baru" required minlength="6">
                                </div>
                                <button type="submit" class="btn-primary">Simpan Password</button>
                            </form>
                        </div>

                        <div class="panel-card">
                            <h3 class="panel-title" style="margin-bottom:10px;">👤 Informasi Profil Akun</h3>
                            <div class="diag-list">
                                <div class="diag-row">
                                    <small style="color:var(--text-muted); font-size:0.72rem;">Nama Lengkap</small>
                                    <div style="font-weight:700; font-size:0.88rem;">{{ $user->name }}</div>
                                </div>
                                <div class="diag-row">
                                    <small style="color:var(--text-muted); font-size:0.72rem;">Alamat Email</small>
                                    <div style="font-weight:700; font-size:0.88rem;">{{ $user->email }}</div>
                                </div>
                                <div class="diag-row">
                                    <small style="color:var(--text-muted); font-size:0.72rem;">Status Login
                                        Password</small>
                                    <div>
                                        @if (empty($user->password))
                                            <span class="status-badge badge-cukup">Belum Diatur (Hanya Login
                                                Google)</span>
                                        @else
                                            <span class="status-badge badge-unggul">Sudah Aktif (Bisa Login Email &
                                                Password)</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </main>
        </div>
    </div>

    <!-- BOTTOM NAVIGATION BAR RESPONSIF KHUSUS HANDPHONE -->
    <nav class="mobile-bottom-nav">
        <button type="button" class="mbn-item active" data-tab="tab-overview">
            <span>📊</span>
            <span>Ringkasan</span>
        </button>
        <button type="button" class="mbn-item" data-tab="tab-diagnostic">
            <span>🎯</span>
            <span>Diagnostik</span>
        </button>
        <button type="button" class="mbn-item" data-tab="tab-tryout">
            <span>📝</span>
            <span>Tryout</span>
        </button>
        <button type="button" class="mbn-item" data-tab="tab-packages">
            <span>🛒</span>
            <span>Paket</span>
        </button>
        <button type="button" class="mbn-item" data-tab="tab-settings">
            <span>🔐</span>
            <span>Akun</span>
        </button>
    </nav>

    <!-- MODAL INFO -->
    <div class="dash-modal" id="dash-modal">
        <div class="dash-modal-box">
            <button type="button" class="dash-modal-close" id="dash-modal-close">&times;</button>
            <div class="dm-icon">🚀</div>
            <h3 id="dm-title">Judul</h3>
            <p id="dm-body">Pesan</p>
            <button type="button" class="btn-primary btn-block" id="dm-ok-btn">Siap, Mengerti!</button>
        </div>
    </div>

    <script src="{{ asset('dashboard/js/dashboard.js') }}"></script>
</body>

</html>
