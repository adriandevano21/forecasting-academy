<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>@yield('title', 'Dashboard Siswa') -
        {{ ($settings['brand_name'] ?? 'Forecasting') . ' ' . ($settings['brand_subname'] ?? 'Academy') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('dashboard/css/dashboard.css') }}">
    @stack('styles')
</head>

<body data-page="{{ Route::currentRouteName() }}" data-dashboard-url="{{ route('dashboard') }}">

    <!-- Custom Cursor Desktop -->
    <div class="cursor-dot" id="cursor-dot"></div>
    <div class="cursor-outline" id="cursor-outline"></div>

    <!-- Animated Background -->
    <div class="animated-bg">
        <canvas id="bg-canvas"></canvas>
        <div class="bg-blob blob-1"></div>
        <div class="bg-blob blob-2"></div>
    </div>

    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <div class="dashboard-wrapper">
        <!-- ==============================================================
             SIDEBAR NAVIGASI UTAMA
             ============================================================== -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('beranda') }}" class="brand-logo" data-cursor-hover>
                    <div class="logo-icon">FA</div>
                    <div class="brand-text">
                        <span class="brand-title">{{ $settings['brand_name'] ?? 'Forecasting' }}</span>
                        <span class="brand-subtitle">{{ $settings['brand_subname'] ?? 'Academy' }}</span>
                    </div>
                </a>
                <button type="button" class="sidebar-close-btn" id="sidebar-close" aria-label="Tutup">&times;</button>
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

            <div class="sidebar-menu-label">MENU UTAMA BELAJAR</div>
            <nav class="sidebar-nav">
                <button type="button"
                    class="nav-item {{ request()->routeIs('dashboard') && request('tab', 'tab-overview') === 'tab-overview' ? 'active' : '' }}"
                    data-tab="tab-overview">
                    <span class="nav-item-left">
                        <span class="nav-ic">📊</span>
                        <span>Ringkasan & Progres</span>
                    </span>
                </button>

                <button type="button"
                    class="nav-item {{ request()->routeIs('dashboard.analysis') || request('tab') === 'tab-diagnostic' ? 'active' : '' }}"
                    data-tab="tab-diagnostic">
                    <span class="nav-item-left">
                        <span class="nav-ic">🎯</span>
                        <span>Analisis Hasil Tryout</span>
                    </span>
                    @if (($attemptsCount ?? 0) > 0)
                        <span class="nav-badge badge-blue">{{ $attemptsCount }} Sesi</span>
                    @elseif (($diagSummary['lemah'] ?? 0) > 0)
                        <span class="nav-badge badge-danger">{{ $diagSummary['lemah'] }}</span>
                    @endif
                </button>

                <button type="button" class="nav-item {{ request('tab') === 'tab-tryout' ? 'active' : '' }}"
                    data-tab="tab-tryout">
                    <span class="nav-item-left">
                        <span class="nav-ic">📝</span>
                        <span>Simulasi Tryout (CBT)</span>
                    </span>
                    <span class="nav-badge badge-blue">{{ $tryoutsCount ?? 0 }}</span>
                </button>

                <button type="button" class="nav-item {{ request('tab') === 'tab-materials' ? 'active' : '' }}"
                    data-tab="tab-materials">
                    <span class="nav-item-left">
                        <span class="nav-ic">🎬</span>
                        <span>Video Materi & Modul</span>
                    </span>
                </button>

                <button type="button" class="nav-item {{ request('tab') === 'tab-psikotes' ? 'active' : '' }}"
                    data-tab="tab-psikotes">
                    <span class="nav-item-left">
                        <span class="nav-ic">🧠</span>
                        <span>Lab Tes Psikologi</span>
                    </span>
                </button>

                <button type="button" class="nav-item {{ request('tab') === 'tab-packages' ? 'active' : '' }}"
                    data-tab="tab-packages">
                    <span class="nav-item-left">
                        <span class="nav-ic">🛒</span>
                        <span>Katalog Paket & Akses</span>
                    </span>
                </button>

                <button type="button" class="nav-item {{ request('tab') === 'tab-settings' ? 'active' : '' }}"
                    data-tab="tab-settings">
                    <span class="nav-item-left">
                        <span class="nav-ic">🔐</span>
                        <span>Pengaturan Akun</span>
                    </span>
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

        <!-- ==============================================================
             MAIN AREA (TOPBAR & KONTEN DINAMIS)
             ============================================================== -->
        <div class="main-area">
            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" class="hamburger-btn" id="sidebar-open" aria-label="Menu">
                        <span></span><span></span><span></span>
                    </button>
                    <div>
                        <h1 class="page-greeting">@yield('topbar_title', 'Halo, ' . explode(' ', $user->name)[0] . '! 👋')</h1>
                        <p class="page-subgreeting">@yield('topbar_subtitle', 'Role: ' . ucfirst($user->role) . ' • ' . $user->email)</p>
                    </div>
                </div>

                <div class="topbar-right">
                    @if (empty($user->password))
                        <button type="button" class="quick-stat-pill pill-warning" data-switch-tab="tab-settings">
                            <span>🔑 Buat Password Login</span>
                        </button>
                    @else
                        <button type="button" class="quick-stat-pill" data-switch-tab="tab-diagnostic">
                            <span>⚡ Kecepatan: <strong>{{ $diagSummary['avg_speed'] ?? 0 }} dtk/soal</strong></span>
                        </button>
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

                @yield('content')
            </main>
        </div>
    </div>

    <!-- ==============================================================
         BOTTOM NAVIGATION BAR (MOBILE PHONE)
         ============================================================== -->
    <nav class="mobile-bottom-nav">
        <button type="button"
            class="mbn-item {{ request()->routeIs('dashboard') && request('tab', 'tab-overview') === 'tab-overview' ? 'active' : '' }}"
            data-tab="tab-overview">
            <span class="mbn-ic">📊</span>
            <span>Ringkasan</span>
        </button>
        <button type="button"
            class="mbn-item {{ request()->routeIs('dashboard.analysis') || request('tab') === 'tab-diagnostic' ? 'active' : '' }}"
            data-tab="tab-diagnostic">
            <span class="mbn-ic">🎯</span>
            <span>Analisis</span>
        </button>
        <button type="button" class="mbn-item {{ request('tab') === 'tab-tryout' ? 'active' : '' }}"
            data-tab="tab-tryout">
            <span class="mbn-ic">📝</span>
            <span>Tryout</span>
        </button>
        <button type="button" class="mbn-item {{ request('tab') === 'tab-packages' ? 'active' : '' }}"
            data-tab="tab-packages">
            <span class="mbn-ic">🛒</span>
            <span>Paket</span>
        </button>
        <button type="button" class="mbn-item {{ request('tab') === 'tab-settings' ? 'active' : '' }}"
            data-tab="tab-settings">
            <span class="mbn-ic">🔐</span>
            <span>Akun</span>
        </button>
    </nav>

    <!-- MODAL INTERAKTIF -->
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
    @stack('scripts')
</body>

</html>
