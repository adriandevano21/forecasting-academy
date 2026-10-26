<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>{{ ($settings['brand_name'] ?? 'Forecasting') . ' ' . ($settings['brand_subname'] ?? 'Academy') }} - Platform
        Persiapan SKD, STIS & Psikologi</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Pemanggilan CSS Terpisah -->
    <link rel="stylesheet" href="{{ asset('beranda/beranda.css') }}">
</head>

<body>

    <!-- Elemen Custom Cursor (Otomatis sembunyi di layar HP/Touchscreen) -->
    <div class="cursor-dot" id="cursor-dot"></div>
    <div class="cursor-outline" id="cursor-outline"></div>

    <!-- Background Beranimasi -->
    <div class="animated-bg">
        <canvas id="bg-canvas"></canvas>
        <div class="bg-blob blob-1"></div>
        <div class="bg-blob blob-2"></div>
        <div class="bg-blob blob-3"></div>
    </div>

    <!-- Navbar Dinamis (Dilengkapi Hamburger Menu untuk Handphone) -->
    <header class="navbar" id="navbar">
        <div class="container nav-container">
            <a href="{{ route('beranda') }}" class="brand-logo" data-cursor-hover>
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
                <div class="brand-text">
                    <span class="brand-title">{{ $settings['brand_name'] ?? 'Forecasting' }}</span>
                    <span class="brand-subtitle">{{ $settings['brand_subname'] ?? 'Academy' }}</span>
                </div>
            </a>

            <!-- Menu Desktop & Drawer Mobile -->
            <nav class="nav-links" id="nav-links">
                @foreach ($navbarMenus as $menu)
                    <a href="{{ $menu->url }}" class="nav-link" data-cursor-hover>{{ $menu->title }}</a>
                @endforeach

                <!-- Tombol Masuk di dalam Drawer Mobile -->
                <div class="mobile-nav-cta">
                    @auth
                        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                            class="btn-login-nav btn-login-mobile">
                            <span>Ke Dashboard</span>
                        </a>
                    @else
                        <button type="button" class="btn-login-nav btn-login-mobile login-modal-trigger">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3" />
                            </svg>
                            <span>Masuk</span>
                        </button>
                    @endauth
                </div>
            </nav>

            <!-- Aksi Kanan (Tombol Masuk Desktop & Hamburger HP) -->
            <div class="nav-actions">
                @auth
                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                        class="btn-login-nav desktop-only" data-cursor-hover>
                        <span>Dashboard Saya</span>
                    </a>
                @else
                    <button type="button" class="btn-login-nav desktop-only" id="btn-login-trigger" data-cursor-hover>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3" />
                        </svg>
                        <span>Masuk</span>
                    </button>
                @endauth

                <!-- Tombol Hamburger khusus Layar Handphone/Tablet -->
                <button type="button" class="mobile-menu-toggle" id="mobile-menu-toggle"
                    aria-label="Buka Menu Navigasi">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Hero Section Dinamis -->
    <section class="hero-section" id="tentang">
        <div class="container hero-grid">
            <div class="hero-content reveal-up">
                @if (!empty($settings['hero_badge']))
                    <div class="badge-pill">
                        <span class="pulse-dot"></span>
                        <span>{{ $settings['hero_badge'] }}</span>
                    </div>
                @endif

                <h1 class="hero-title">
                    {{ $settings['hero_title_prefix'] ?? '' }}
                    <span class="text-gradient">{{ $settings['hero_title_highlight'] ?? '' }}</span>
                    {{ $settings['hero_title_suffix'] ?? '' }}
                </h1>

                @if (!empty($settings['hero_description']))
                    <p class="hero-description">{{ $settings['hero_description'] }}</p>
                @endif

                <div class="hero-cta-group">
                    @if (!empty($settings['hero_primary_btn']))
                        <a href="#paket" class="btn-primary btn-lg" data-cursor-hover>
                            <span>{{ $settings['hero_primary_btn'] }}</span>
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    @endif
                    @if (!empty($settings['hero_secondary_btn']))
                        <a href="#fitur" class="btn-secondary btn-lg" data-cursor-hover>
                            <span>{{ $settings['hero_secondary_btn'] }}</span>
                        </a>
                    @endif
                </div>

                @if (!empty($settings['hero_stats']) && is_array($settings['hero_stats']))
                    <div class="hero-stats">
                        @foreach ($settings['hero_stats'] as $index => $stat)
                            @if ($index > 0)
                                <div class="stat-divider"></div>
                            @endif
                            <div class="stat-item">
                                <span
                                    class="stat-number">{{ $stat['number'] }}<small>{{ $stat['unit'] }}</small></span>
                                <span class="stat-label">{{ $stat['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Ilustrasi Interaktif Dashboard Diagnostic & Timer Soal -->
            <div class="hero-visual reveal-up">
                <div class="mockup-card main-mockup">
                    <div class="mockup-header">
                        <div class="window-dots">
                            <span></span><span></span><span></span>
                        </div>
                        <span class="mockup-title">Live Competency Diagnostic</span>
                        <span class="badge-live">Real-time</span>
                    </div>

                    <div class="mockup-body">
                        @if ($sampleQuestion)
                            <div class="question-timer-preview">
                                <div class="qt-info">
                                    <span class="qt-badge">{{ $sampleQuestion->topic_name }}</span>
                                    <h4>{{ \Illuminate\Support\Str::limit($sampleQuestion->question_text, 48) }}</h4>
                                </div>
                                <div class="qt-clock">
                                    <div class="qt-time-val">38<small>dtk</small></div>
                                    <span class="qt-ideal">Target Ideal: {{ $sampleQuestion->ideal_time_seconds }}
                                        dtk</span>
                                </div>
                            </div>
                        @endif

                        <div class="diagnostic-bars">
                            @foreach ($sampleDiagnostics as $diag)
                                @php
                                    $statusClass = strtolower($diag->status_label);
                                @endphp
                                <div class="diag-item">
                                    <div class="diag-head">
                                        <span class="diag-topic">{{ $diag->topic_name }}</span>
                                        <span class="diag-status status-{{ $statusClass }}">
                                            {{ $diag->status_label }}
                                            ({{ number_format($diag->accuracy_percentage, 0) }}% •
                                            {{ number_format($diag->avg_time_seconds, 0) }} dtk/soal)
                                        </span>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-fill fill-{{ $statusClass }}"
                                            style="width: {{ $diag->accuracy_percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="recommendation-alert">
                            <div class="rec-icon">💡</div>
                            <div class="rec-text">
                                <strong>Rekomendasi Sistem:</strong> Topik berstatus <em>Lemah</em> terdeteksi otomatis
                                beserta analisis waktu pengerjaan untuk menentukan video materi yang wajib Anda tonton!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur Unggulan Section -->
    <section class="features-section" id="fitur">
        <div class="container">
            <div class="section-header reveal-up">
                @if (!empty($settings['feature_section_tag']))
                    <span class="section-tag">{{ $settings['feature_section_tag'] }}</span>
                @endif
                <h2 class="section-title">{{ $settings['feature_section_title'] ?? 'Fitur Unggulan' }}</h2>
                @if (!empty($settings['feature_section_subtitle']))
                    <p class="section-subtitle">{{ $settings['feature_section_subtitle'] }}</p>
                @endif
            </div>

            <div class="features-grid">
                @foreach ($features as $feat)
                    <div class="feature-card reveal-up" data-cursor-hover>
                        <div class="feature-icon icon-{{ $feat->color_theme }}">
                            @switch($feat->icon_key)
                                @case('chart')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                @break

                                @case('clock')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @break

                                @case('brain')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                @break

                                @case('video')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @break

                                @case('layers')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                @break

                                @default
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                            @endswitch
                        </div>
                        <h3>{{ $feat->title }}</h3>
                        <p>{{ $feat->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Program & Silabus Section -->
    <section class="programs-section" id="program">
        <div class="container">
            <div class="section-header reveal-up">
                @if (!empty($settings['program_section_tag']))
                    <span class="section-tag">{{ $settings['program_section_tag'] }}</span>
                @endif
                <h2 class="section-title">{{ $settings['program_section_title'] ?? 'Fokus Program' }}</h2>
                @if (!empty($settings['program_section_subtitle']))
                    <p class="section-subtitle">{{ $settings['program_section_subtitle'] }}</p>
                @endif
            </div>

            <div class="programs-grid">
                @foreach ($programs as $prog)
                    <div class="program-card reveal-up" data-cursor-hover>
                        <div class="prog-badge">{{ $prog->badge_text ?? 'Program Prioritas' }}</div>
                        <h3 class="prog-title">{{ $prog->name }}</h3>
                        <p class="prog-desc">{{ $prog->description }}</p>

                        <div class="subjects-list">
                            <span class="subj-heading">Cakupan Mata Uji & Passing Grade:</span>
                            @if (count($prog->subjects) > 0)
                                @foreach ($prog->subjects as $subj)
                                    <div class="subject-item">
                                        <span class="subj-name">{{ $subj->name }}</span>
                                        <span class="subj-pg">PG: {{ $subj->passing_grade }} Poin</span>
                                    </div>
                                @endforeach
                            @elseif($prog->custom_stat_label)
                                <div class="subject-item">
                                    <span class="subj-name">{{ $prog->custom_stat_label }}</span>
                                    <span class="subj-pg">Aktif</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Paket Pembelajaran Section -->
    <section class="packages-section" id="paket">
        <div class="container">
            <div class="section-header reveal-up">
                @if (!empty($settings['package_section_tag']))
                    <span class="section-tag">{{ $settings['package_section_tag'] }}</span>
                @endif
                <h2 class="section-title">{{ $settings['package_section_title'] ?? 'Pilihan Paket Belajar' }}</h2>
                @if (!empty($settings['package_section_subtitle']))
                    <p class="section-subtitle">{{ $settings['package_section_subtitle'] }}</p>
                @endif
            </div>

            <!-- Wrapper Filter (Bisa di-swipe ke samping di layar Handphone) -->
            <div class="package-filters-wrapper reveal-up">
                <div class="package-filters">
                    <button class="filter-btn active" data-filter="all" data-cursor-hover>Semua Paket</button>
                    <button class="filter-btn" data-filter="bundling" data-cursor-hover>🔥 Bundling (TO +
                        Materi)</button>
                    <button class="filter-btn" data-filter="to_full" data-cursor-hover>Tryout Full SKD/STIS</button>
                    <button class="filter-btn" data-filter="to_materi" data-cursor-hover>TO Spesialisasi
                        Materi</button>
                    <button class="filter-btn" data-filter="private_1on1" data-cursor-hover>Private 1-on-1 &
                        Custom</button>
                </div>
            </div>

            <div class="packages-grid">
                @foreach ($packages as $pkg)
                    <div class="package-card {{ $pkg->is_featured ? 'featured-package' : '' }} reveal-up"
                        data-category="{{ $pkg->type }}" data-cursor-hover>
                        @if ($pkg->is_featured)
                            <div class="popular-ribbon">PALING DIREKOMENDASIKAN</div>
                        @endif

                        <div class="pkg-header">
                            <span class="pkg-type-badge">{{ $pkg->badge_text ?? 'Paket Belajar' }}</span>
                            <h3 class="pkg-name">{{ $pkg->name }}</h3>
                            @if ($pkg->short_description)
                                <p class="pkg-short-desc">{{ $pkg->short_description }}</p>
                            @endif
                        </div>

                        <div class="pkg-pricing">
                            @if ($pkg->discount_price)
                                <div class="price-strike">Rp {{ number_format($pkg->price, 0, ',', '.') }}</div>
                                <div class="price-main">
                                    <span class="currency">Rp</span>
                                    <span class="amount">{{ number_format($pkg->discount_price, 0, ',', '.') }}</span>
                                </div>
                            @else
                                <div class="price-main">
                                    <span class="currency">Rp</span>
                                    <span class="amount">{{ number_format($pkg->price, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <span class="pkg-period">Masa aktif {{ $pkg->active_period_days }} Hari (Akses
                                24/7)</span>
                        </div>

                        <ul class="pkg-features">
                            @foreach ($pkg->features_list as $featItem)
                                <li>
                                    <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ $featItem }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="pkg-footer">
                            <button type="button"
                                class="btn-choose-pkg {{ $pkg->is_featured ? 'btn-primary' : 'btn-outline' }} login-modal-trigger"
                                data-package="{{ $pkg->name }}" data-cursor-hover>
                                <span>Ambil Paket Ini</span>
                                <svg width="18" height="18" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Popup Modal Login (Pilihan Email & Password ATAU Google) -->
    <div class="modal-overlay {{ $errors->any() || session('error') ? 'active' : '' }}" id="login-modal">
        <div class="modal-box">
            <button class="modal-close" id="modal-close" data-cursor-hover aria-label="Tutup Modal">&times;</button>

            <div class="modal-header-badge">🎓 Forecasting Academy</div>
            <h3 class="modal-title">Masuk ke Akun Anda</h3>
            <p class="modal-subtitle" id="modal-selected-pkg">
                Silakan masuk menggunakan Email & Password atau lanjutkan instan dengan akun Google.
            </p>

            <!-- Notifikasi Error jika gagal login -->
            @if ($errors->any())
                <div class="modal-alert-error">
                    ⚠️ {{ $errors->first() }}
                </div>
            @endif
            @if (session('error'))
                <div class="modal-alert-error">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            <!-- Pilihan 1: Form Email & Password -->
            <form action="{{ route('login.post') }}" method="POST" class="modal-login-form">
                @csrf
                <div class="form-group-modal">
                    <label for="login-email">Alamat Email</label>
                    <div class="input-icon-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <input type="email" id="login-email" name="email" value="{{ old('email') }}"
                            placeholder="nama@email.com" required>
                    </div>
                </div>

                <div class="form-group-modal">
                    <label for="login-password">Password</label>
                    <div class="input-icon-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <input type="password" id="login-password" name="password"
                            placeholder="Masukkan password Anda" required>
                        <button type="button" class="toggle-pass-btn" id="toggle-pass-btn"
                            aria-label="Lihat Password">👁️</button>
                    </div>
                </div>

                <div class="form-options-modal">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" checked>
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit" class="btn-primary btn-submit-login" data-cursor-hover>
                    <span>Masuk dengan Email</span>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>

            <!-- Garis Pemisah -->
            <div class="modal-divider">
                <span>atau masuk lebih cepat dengan</span>
            </div>

            <!-- Pilihan 2: Tombol Login dengan Google -->
            <a href="{{ route('login.google') }}" class="btn-google-full" data-cursor-hover>
                <svg class="google-icon" viewBox="0 0 24 24">
                    <path fill="#4285F4"
                        d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z" />
                    <path fill="#34A853"
                        d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.11-6.72-4.96H1.29v3.14C3.26 21.3 7.31 24 12 24z" />
                    <path fill="#FBBC05"
                        d="M5.28 14.24c-.24-.72-.38-1.49-.38-2.24s.14-1.52.38-2.24V6.62H1.29C.47 8.24 0 10.06 0 12s.47 3.76 1.29 5.38l3.99-3.14z" />
                    <path fill="#EA4335"
                        d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.31 0 3.26 2.7 1.29 6.62l3.99 3.14c.95-2.85 3.6-4.96 6.72-4.96z" />
                </svg>
                <span>Lanjutkan dengan Google</span>
            </a>

            <div class="modal-note">
                🔒 Riwayat tryout, progres video, dan <em>Competency Diagnostic</em> tersimpan aman di akun Anda.
            </div>
        </div>
    </div>

    <!-- Footer Lengkap Dinamis -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Kolom 1: Brand & Deskripsi -->
                <div class="footer-col">
                    <div class="brand-logo footer-logo">
                        <div class="logo-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                        </div>
                        <div class="brand-text">
                            <span class="brand-title">{{ $settings['brand_name'] ?? 'Forecasting' }}</span>
                            <span class="brand-subtitle">{{ $settings['brand_subname'] ?? 'Academy' }}</span>
                        </div>
                    </div>
                    @if (!empty($settings['footer_about']))
                        <p class="footer-about-text">{{ $settings['footer_about'] }}</p>
                    @endif

                    @if ($footerSocials->count() > 0)
                        <div class="footer-socials">
                            @foreach ($footerSocials as $soc)
                                <a href="{{ $soc->value }}" target="_blank" rel="noopener" class="social-pill"
                                    data-cursor-hover>
                                    {{ $soc->label }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Kolom 2: Tautan Cepat -->
                @if ($footerQuicklinks->count() > 0)
                    <div class="footer-col">
                        <h4 class="footer-heading">Navigasi Cepat</h4>
                        <ul class="footer-links">
                            @foreach ($footerQuicklinks as $qlink)
                                <li><a href="{{ $qlink->url }}" data-cursor-hover>{{ $qlink->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Kolom 3: Informasi Kontak & Alamat -->
                @if ($footerContacts->count() > 0)
                    <div class="footer-col">
                        <h4 class="footer-heading">Hubungi Kami & Alamat</h4>
                        <ul class="footer-contact-list">
                            @foreach ($footerContacts as $contact)
                                <li>
                                    <span class="contact-label">{{ $contact->label }}:</span>
                                    <span class="contact-val">{{ $contact->value }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            @if (!empty($settings['footer_copyright']))
                <div class="footer-bottom">
                    &copy; {{ date('Y') }} {{ $settings['footer_copyright'] }}
                </div>
            @endif
        </div>
    </footer>

    <!-- Pemanggilan JavaScript Terpisah -->
    <script src="{{ asset('beranda/beranda.js') }}"></script>
</body>

</html>
