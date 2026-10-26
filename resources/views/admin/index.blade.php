<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Admin Control Center - Forecasting Academy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('dashboard/admin/css/admin.css') }}">
</head>

<body data-active-tab="{{ session('active_tab', request()->has('tryout_id') ? 'tab-questions' : 'tab-overview') }}">

    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <div class="admin-wrapper">
        <!-- SIDEBAR -->
        <aside class="admin-sidebar" id="admin-sidebar">
            <div class="sb-top">
                <a href="{{ route('beranda') }}" class="sb-brand" target="_blank">
                    <div class="sb-logo">FA</div>
                    <div>
                        <span class="sb-title">Forecasting</span>
                        <span class="sb-sub">Admin Control Panel</span>
                    </div>
                </a>
                <button type="button" class="sb-close" id="sb-close">&times;</button>
            </div>

            <div class="sb-admin-profile">
                <span class="sap-badge">🛡️ SUPER ADMIN</span>
                <h4>{{ $admin->name }}</h4>
                <small>{{ $admin->email }}</small>
            </div>

            <div class="sb-label">MENU MANAJEMEN</div>
            <nav class="sb-nav">
                <button type="button" class="sb-link active" data-tab="tab-overview">
                    <span>📊 Ringkasan & Alur</span>
                </button>
                <button type="button" class="sb-link" data-tab="tab-tryouts">
                    <span>🗂️ 1. Master Paket Tryout</span>
                    <span class="sb-pill">{{ $tryouts->count() }}</span>
                </button>
                <button type="button" class="sb-link" data-tab="tab-questions">
                    <span>❓ 2. Bank Soal & Opsi</span>
                    <span class="sb-pill">{{ $summary['totalQuestions'] }}</span>
                </button>
                <button type="button" class="sb-link" data-tab="tab-materials">
                    <span>🎬 3. Video Materi & Modul</span>
                    <span class="sb-pill">{{ $materials->count() }}</span>
                </button>
                <button type="button" class="sb-link" data-tab="tab-packages">
                    <span>📦 4. Paket & Isi Bundling</span>
                    <span class="sb-pill">{{ $packages->count() }}</span>
                </button>
                <button type="button" class="sb-link" data-tab="tab-curriculum">
                    <span>📚 5. Program, Mapel & Topik</span>
                </button>
                <button type="button" class="sb-link" data-tab="tab-cms">
                    <span>🌐 6. CMS Tampilan Beranda</span>
                </button>
                <button type="button" class="sb-link" data-tab="tab-users">
                    <span>👥 7. Manajemen Pengguna</span>
                    <span class="sb-pill">{{ $users->count() }}</span>
                </button>
                <button type="button" class="sb-link" data-tab="tab-transactions">
                    <span>💳 8. Transaksi & Akses</span>
                </button>
                <button type="button" class="sb-link" data-tab="tab-results">
                    <span>📈 9. Hasil & Diagnostik</span>
                </button>
            </nav>

            <div class="sb-footer">
                <a href="{{ route('beranda') }}" target="_blank" class="btn-view-site">👀 Lihat Beranda Utama
                    &nearr;</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">Keluar (Logout)</button>
                </form>
            </div>
        </aside>

        <!-- MAIN AREA -->
        <div class="admin-main">
            <header class="admin-topbar">
                <div class="at-left">
                    <button type="button" class="hamburger-btn" id="sb-open">
                        <span></span><span></span><span></span>
                    </button>
                    <div>
                        <h1 class="at-title">Control Center Forecasting Academy</h1>
                        <p class="at-sub">Klik tombol <strong>+ Tambah</strong> atau <strong>Edit</strong> untuk membuka
                            Pop-Up Modal</p>
                    </div>
                </div>
                <div class="at-right">
                    <span class="top-badge">Total Bank Soal: <strong>{{ $summary['totalQuestions'] }}
                            Soal</strong></span>
                </div>
            </header>

            <main class="admin-container">

                @if (session('success'))
                    <div class="adm-alert adm-alert-success">
                        <span>✅ {{ session('success') }}</span>
                        <button type="button" onclick="this.parentElement.remove()">&times;</button>
                    </div>
                @endif
                @if (session('error') || $errors->any())
                    <div class="adm-alert adm-alert-danger">
                        <span>⚠️ {{ session('error') ?? $errors->first() }}</span>
                        <button type="button" onclick="this.parentElement.remove()">&times;</button>
                    </div>
                @endif

                <!-- ==========================================================
                     TAB OVERVIEW
                     ========================================================== -->
                <section class="adm-pane active" id="tab-overview">
                    <div class="kpi-grid">
                        <div class="kpi-card" data-go-tab="tab-tryouts">
                            <div class="kpi-icon bg-blue">🗂️</div>
                            <div>
                                <small>Master Paket Tryout</small>
                                <h3>{{ $summary['tryouts'] }} Paket</h3>
                            </div>
                        </div>
                        <div class="kpi-card" data-go-tab="tab-questions">
                            <div class="kpi-icon bg-amber">❓</div>
                            <div>
                                <small>Total Bank Soal</small>
                                <h3>{{ $summary['totalQuestions'] }} Butir</h3>
                            </div>
                        </div>
                        <div class="kpi-card" data-go-tab="tab-packages">
                            <div class="kpi-icon bg-emerald">📦</div>
                            <div>
                                <small>Paket Bundling Dijual</small>
                                <h3>{{ $summary['packages'] }} Katalog</h3>
                            </div>
                        </div>
                        <div class="kpi-card" data-go-tab="tab-transactions">
                            <div class="kpi-icon bg-rose">💰</div>
                            <div>
                                <small>Pendapatan ({{ $summary['paidTrx'] }} Lunas)</small>
                                <h3>Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="adm-card mb-14">
                        <div class="adm-card-head">
                            <div>
                                <h3>🚀 Alur Kerja Terpadu (Semua Form via Pop-Up Modal)</h3>
                                <p>Jumlah soal pada setiap Paket Tryout bersifat fleksibel (contoh awal 20 soal, bisa
                                    ditambah hingga 110+ soal).</p>
                            </div>
                        </div>
                        <div class="workflow-steps">
                            <div class="wf-step" data-go-tab="tab-tryouts">
                                <span class="wf-num">Langkah 1</span>
                                <h4>Master Paket Tryout</h4>
                                <p>Buat wadah ujian Tryout beserta durasi & instruksi pengerjaan.</p>
                            </div>
                            <div class="wf-step" data-go-tab="tab-questions">
                                <span class="wf-num">Langkah 2</span>
                                <h4>Bank Soal & Opsi Gambar</h4>
                                <p>Tambah soal, gambar soal, pembahasan, & opsi A-E (teks/link gambar).</p>
                            </div>
                            <div class="wf-step" data-go-tab="tab-materials">
                                <span class="wf-num">Langkah 3</span>
                                <h4>Video Materi & Modul</h4>
                                <p>Kelola konten video pembelajaran per sub-topik bersama Mentor.</p>
                            </div>
                            <div class="wf-step" data-go-tab="tab-packages">
                                <span class="wf-num">Langkah 4</span>
                                <h4>Rakit Paket Bundling</h4>
                                <p>Centang Paket Tryout, Video Materi, & Sesi Private 1-on-1 dalam pop-up.</p>
                            </div>
                        </div>
                    </div>

                    <div class="adm-card">
                        <div class="adm-card-head">
                            <div>
                                <h3>📋 Daftar Master Paket Tryout & Jumlah Soal Saat Ini</h3>
                                <p>Klik tombol <strong>Kelola Soal</strong> untuk menambah soal ke-21, ke-22, dst. pada
                                    paket tersebut.</p>
                            </div>
                            <button type="button" class="btn-primary" onclick="openTryoutModal()">+ Tambah Paket
                                Tryout</button>
                        </div>
                        <div class="table-wrap">
                            <table class="adm-table">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Master Paket Tryout</th>
                                        <th>Program</th>
                                        <th>Durasi</th>
                                        <th>Soal Terisi / Target</th>
                                        <th>Pintasan Bank Soal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tryouts as $to)
                                        <tr>
                                            <td><span class="badge-tag">{{ $to->code }}</span></td>
                                            <td><strong>{{ $to->title }}</strong></td>
                                            <td>{{ $to->program_name ?? 'Umum' }}</td>
                                            <td>{{ $to->duration_minutes }} Menit</td>
                                            <td>
                                                <span class="toggle-btn on">
                                                    {{ $to->actual_questions_count }} Soal (Target:
                                                    {{ $to->total_questions }})
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.dashboard', ['tryout_id' => $to->id]) }}"
                                                    class="btn-xs btn-primary">
                                                    Kelola Soal ({{ $to->actual_questions_count }}) &rarr;
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- ==========================================================
                     TAB 1: MASTER PAKET TRYOUT (`tryouts`)
                     ========================================================== -->
                <section class="adm-pane" id="tab-tryouts">
                    <div class="adm-card">
                        <div class="adm-card-head">
                            <div>
                                <h3>🗂️ Master Paket Tryout (`tryouts`)</h3>
                                <p>Jumlah target soal bersifat fleksibel (misal: 20, 45, 60, atau 110 soal) dan bisa
                                    ditambah kapan saja.</p>
                            </div>
                            <button type="button" class="btn-primary" onclick="openTryoutModal()">+ Tambah Paket
                                Tryout Baru</button>
                        </div>

                        <div class="table-wrap">
                            <table class="adm-table">
                                <thead>
                                    <tr>
                                        <th>Kode & Nama Paket Tryout</th>
                                        <th>Program & Tipe</th>
                                        <th>Durasi</th>
                                        <th>Jumlah Soal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tryouts as $to)
                                        <tr>
                                            <td>
                                                <span class="badge-tag">{{ $to->code }}</span>
                                                <strong class="d-block mt-4">{{ $to->title }}</strong>
                                                <small class="text-muted">{{ $to->instruction }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $to->program_name ?? 'Umum' }}</strong><br>
                                                <small class="text-muted">{{ $to->type }}</small>
                                            </td>
                                            <td>{{ $to->duration_minutes }} Menit</td>
                                            <td>
                                                <a href="{{ route('admin.dashboard', ['tryout_id' => $to->id]) }}"
                                                    class="btn-xs btn-outline">
                                                    ❓ {{ $to->actual_questions_count }} / {{ $to->total_questions }}
                                                    Soal &rarr;
                                                </a>
                                            </td>
                                            <td>
                                                <button type="button"
                                                    class="toggle-btn {{ $to->is_active ? 'on' : 'off' }}"
                                                    onclick="toggleStatus('tryouts', {{ $to->id }}, 'is_active', 'tab-tryouts')">
                                                    {{ $to->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn-xs btn-edit"
                                                    onclick='openTryoutModal(@json($to))'>✏️
                                                    Edit</button>
                                                <button type="button" class="btn-xs btn-danger"
                                                    onclick="deleteRecord('tryouts', {{ $to->id }}, 'tab-tryouts')">Hapus</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- ==========================================================
                     TAB 2: BANK SOAL, GAMBAR SOAL, PEMBAHASAN & GAMBAR OPSI (`questions`)
                     ========================================================== -->
                <section class="adm-pane" id="tab-questions">
                    <div class="adm-card mb-14">
                        <div class="flex-between">
                            <div>
                                <h3>❓ Bank Soal, Pembahasan & Opsi Jawaban (Teks / Gambar)</h3>
                                <p class="text-muted">Pilih Paket Tryout di samping, atau klik <strong>+ Tambah Butir
                                        Soal Baru</strong> untuk membuka Pop-Up pembuatan soal.</p>
                            </div>
                            <div class="flex-gap">
                                <form action="{{ route('admin.dashboard') }}" method="GET">
                                    <select name="tryout_id" class="adm-input" onchange="this.form.submit()"
                                        style="min-width:250px; font-weight:700;">
                                        @foreach ($tryouts as $t)
                                            <option value="{{ $t->id }}"
                                                {{ (string) $selectedTryoutId === (string) $t->id ? 'selected' : '' }}>
                                                [{{ $t->code }}] {{ Str::limit($t->title, 34) }}
                                                ({{ $t->actual_questions_count }} Soal)
                                            </option>
                                        @endforeach
                                        <option value="all" {{ $selectedTryoutId === 'all' ? 'selected' : '' }}>
                                            Tampilkan Semua Paket ({{ $summary['totalQuestions'] }} Soal)</option>
                                    </select>
                                </form>
                                <button type="button" class="btn-primary"
                                    onclick="openQuestionModal(null, {{ $selectedTryoutId !== 'all' ? (int) $selectedTryoutId : $tryouts->first()->id ?? 1 }}, {{ $nextQuestionNumber }})">
                                    + Tambah Soal Baru (No. {{ $nextQuestionNumber }})
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="adm-card">
                        <div class="table-wrap">
                            <table class="adm-table">
                                <thead>
                                    <tr>
                                        <th style="width:72px;">No</th>
                                        <th style="width:155px;">Paket & Topik</th>
                                        <th>Pertanyaan, Gambar Soal & Opsi Jawaban (A-E)</th>
                                        <th style="width:260px;">Pembahasan & Gambar</th>
                                        <th style="width:115px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($questions as $q)
                                        <tr>
                                            <td>
                                                <span class="q-num-badge">No. {{ $q->question_number }}</span>
                                                <small class="d-block text-muted mt-4">⏱️
                                                    {{ $q->ideal_time_seconds }}s</small>
                                            </td>
                                            <td>
                                                <span class="badge-tag">{{ $q->tryout_code }}</span>
                                                <strong class="d-block mt-4">{{ $q->topic_name }}</strong>
                                            </td>
                                            <td>
                                                <div class="q-preview">{{ $q->question_text }}</div>
                                                @if ($q->question_image)
                                                    <div class="img-preview-row">
                                                        <img src="{{ $q->question_image }}" alt="Soal"
                                                            class="adm-thumb">
                                                        <a href="{{ $q->question_image }}" target="_blank"
                                                            class="link-mini">🔗 Gambar Soal</a>
                                                    </div>
                                                @endif
                                                <div class="opt-pills mt-6">
                                                    @foreach ($q->options as $op)
                                                        <div
                                                            class="opt-pill {{ $op->score_weight >= 5 ? 'correct' : '' }}">
                                                            <strong>{{ $op->option_label }}:</strong>
                                                            {{ Str::limit($op->option_text, 24) }}
                                                            ({{ $op->score_weight }})
                                                            @if ($op->option_image)
                                                                <a href="{{ $op->option_image }}" target="_blank"
                                                                    class="opt-img-tag"
                                                                    title="Lihat Gambar Opsi {{ $op->option_label }}">🖼️
                                                                    Gambar</a>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <div class="disc-preview">
                                                    {{ Str::limit($q->discussion_text, 100) ?: '—' }}</div>
                                                @if ($q->discussion_image)
                                                    <div class="img-preview-row mt-4">
                                                        <img src="{{ $q->discussion_image }}" alt="Pembahasan"
                                                            class="adm-thumb">
                                                        <a href="{{ $q->discussion_image }}" target="_blank"
                                                            class="link-mini">🔗 Gambar Bahasan</a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn-xs btn-edit"
                                                    onclick='openQuestionModal(@json($q))'>✏️
                                                    Edit</button>
                                                <button type="button" class="btn-xs btn-danger"
                                                    onclick="deleteRecord('questions', {{ $q->id }}, 'tab-questions')">Hapus</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- ==========================================================
                     TAB 3: VIDEO MATERI & MODUL (`materials`)
                     ========================================================== -->
                <section class="adm-pane" id="tab-materials">
                    <div class="adm-card">
                        <div class="adm-card-head">
                            <div>
                                <h3>🎬 Video Pembelajaran & Modul PDF (`materials`)</h3>
                                <p>Materi yang dibuat di sini otomatis muncul sebagai pilihan isi *Bundling* pada Paket
                                    Belajar.</p>
                            </div>
                            <button type="button" class="btn-primary" onclick="openMaterialModal()">+ Tambah Video /
                                Modul</button>
                        </div>

                        <div class="table-wrap">
                            <table class="adm-table">
                                <thead>
                                    <tr>
                                        <th>ID & Judul Materi</th>
                                        <th>Topik & Mentor</th>
                                        <th>Tipe & Durasi</th>
                                        <th>Akses Preview</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($materials as $mt)
                                        <tr>
                                            <td><strong>#{{ $mt->id }} - {{ $mt->title }}</strong><br><small
                                                    class="text-muted">{{ $mt->content_url }}</small></td>
                                            <td><span
                                                    class="badge-tag">{{ $mt->topic_name }}</span><br><small>{{ $mt->instructor_name }}</small>
                                            </td>
                                            <td>{{ strtoupper($mt->type) }} ({{ ceil($mt->duration / 60) }} mnt)</td>
                                            <td>
                                                <button type="button"
                                                    class="toggle-btn {{ $mt->is_free_preview ? 'on' : 'off' }}"
                                                    onclick="toggleStatus('materials', {{ $mt->id }}, 'is_free_preview', 'tab-materials')">
                                                    {{ $mt->is_free_preview ? 'Gratis' : 'Berbayar' }}
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn-xs btn-edit"
                                                    onclick='openMaterialModal(@json($mt))'>✏️
                                                    Edit</button>
                                                <button type="button" class="btn-xs btn-danger"
                                                    onclick="deleteRecord('materials', {{ $mt->id }}, 'tab-materials')">Hapus</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- ==========================================================
                     TAB 4: PAKET PENJUALAN & ISI BUNDLING (`packages`)
                     ========================================================== -->
                <section class="adm-pane" id="tab-packages">
                    <div class="adm-card">
                        <div class="adm-card-head">
                            <div>
                                <h3>📦 Katalog Paket Belajar & Isi Bundling (`packages` & `package_items`)</h3>
                                <p>Klik <strong>+ Buat Paket Baru</strong> atau <strong>✏️ Edit</strong> untuk memilih
                                    isi paket dari daftar Tryout, Video Materi, dan Private 1-on-1.</p>
                            </div>
                            <button type="button" class="btn-primary" onclick="openPackageModal()">+ Buat Paket &
                                Bundling Baru</button>
                        </div>

                        <div class="table-wrap">
                            <table class="adm-table">
                                <thead>
                                    <tr>
                                        <th>Urutan, Nama Paket & Harga</th>
                                        <th>Isi Bundling (Tryout, Video Materi & Private 1-on-1)</th>
                                        <th>Tampil Beranda</th>
                                        <th>Highlight</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($packages as $pkg)
                                        <tr>
                                            <td>
                                                <span class="badge-tag">{{ $pkg->type }}</span>
                                                <strong class="d-block mt-4">#{{ $pkg->sort_order }} -
                                                    {{ $pkg->name }}</strong>
                                                <div class="mt-4">
                                                    @if ($pkg->discount_price)
                                                        <s class="text-muted">Rp
                                                            {{ number_format($pkg->price, 0, ',', '.') }}</s>
                                                        <strong class="text-blue"> Rp
                                                            {{ number_format($pkg->discount_price, 0, ',', '.') }}</strong>
                                                    @else
                                                        <strong>Rp {{ number_format($pkg->price, 0, ',', '.') }}</strong>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="bundle-pills-wrap">
                                                    @foreach ($pkg->bundled_tryouts as $bt)
                                                        <span class="bundle-pill pill-to">🗂️
                                                            {{ $bt }}</span>
                                                    @endforeach
                                                    @foreach ($pkg->bundled_materials as $bm)
                                                        <span class="bundle-pill pill-mat">🎬
                                                            {{ Str::limit($bm, 32) }}</span>
                                                    @endforeach
                                                    @if ($pkg->private_sessions_count > 0 || count($pkg->bundled_mentors) > 0)
                                                        <span class="bundle-pill pill-priv">👨‍🏫
                                                            {{ $pkg->private_sessions_count }}x Private 1-on-1
                                                            ({{ implode(', ', $pkg->bundled_mentors) }})</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button"
                                                    class="toggle-btn {{ $pkg->show_on_homepage ? 'on' : 'off' }}"
                                                    onclick="toggleStatus('packages', {{ $pkg->id }}, 'show_on_homepage', 'tab-packages')">
                                                    {{ $pkg->show_on_homepage ? 'Tampil' : 'Sembunyi' }}
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button"
                                                    class="toggle-btn {{ $pkg->is_featured ? 'on' : 'off' }}"
                                                    onclick="toggleStatus('packages', {{ $pkg->id }}, 'is_featured', 'tab-packages')">
                                                    {{ $pkg->is_featured ? 'Ya' : 'Tidak' }}
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn-xs btn-edit"
                                                    onclick='openPackageModal(@json($pkg))'>✏️
                                                    Edit</button>
                                                <button type="button" class="btn-xs btn-danger"
                                                    onclick="deleteRecord('packages', {{ $pkg->id }}, 'tab-packages')">Hapus</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- ==========================================================
                     TAB 5: KURIKULUM (`programs`, `subjects`, `topics`)
                     ========================================================== -->
                <section class="adm-pane" id="tab-curriculum">
                    <div class="adm-card mb-14">
                        <div class="adm-card-head">
                            <h3>1. Program Utama (`programs`)</h3>
                            <button type="button" class="btn-primary" onclick="openProgramModal()">+ Tambah
                                Program</button>
                        </div>
                        <div class="table-wrap">
                            <table class="adm-table">
                                <thead>
                                    <tr>
                                        <th>Urutan & Program</th>
                                        <th>Deskripsi</th>
                                        <th>Tampil Beranda</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($programs as $pr)
                                        <tr>
                                            <td><strong>#{{ $pr->sort_order }} -
                                                    {{ $pr->name }}</strong><br><small
                                                    class="text-muted">{{ $pr->badge_text }}</small></td>
                                            <td>{{ $pr->description }}</td>
                                            <td>
                                                <button type="button"
                                                    class="toggle-btn {{ $pr->show_on_homepage ? 'on' : 'off' }}"
                                                    onclick="toggleStatus('programs', {{ $pr->id }}, 'show_on_homepage', 'tab-curriculum')">
                                                    {{ $pr->show_on_homepage ? 'Tampil' : 'Sembunyi' }}
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn-xs btn-edit"
                                                    onclick='openProgramModal(@json($pr))'>✏️
                                                    Edit</button>
                                                <button type="button" class="btn-xs btn-danger"
                                                    onclick="deleteRecord('programs', {{ $pr->id }}, 'tab-curriculum')">Hapus</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="adm-two-col">
                        <div class="adm-card">
                            <div class="adm-card-head">
                                <h3>2. Mata Uji (`subjects`)</h3>
                                <button type="button" class="btn-primary" onclick="openSubjectModal()">+ Tambah
                                    Mapel</button>
                            </div>
                            <div class="table-wrap">
                                <table class="adm-table">
                                    <thead>
                                        <tr>
                                            <th>Mata Uji</th>
                                            <th>Program</th>
                                            <th>PG</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subjects as $sb)
                                            <tr>
                                                <td><strong>{{ $sb->name }}</strong></td>
                                                <td><span class="badge-tag">{{ $sb->program_name }}</span></td>
                                                <td>{{ $sb->passing_grade }}</td>
                                                <td>
                                                    <button type="button" class="btn-xs btn-edit"
                                                        onclick='openSubjectModal(@json($sb))'>✏️
                                                        Edit</button>
                                                    <button type="button" class="btn-xs btn-danger"
                                                        onclick="deleteRecord('subjects', {{ $sb->id }}, 'tab-curriculum')">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="adm-card">
                            <div class="adm-card-head">
                                <h3>3. Sub-Topik (`topics`)</h3>
                                <button type="button" class="btn-primary" onclick="openTopicModal()">+ Tambah
                                    Topik</button>
                            </div>
                            <div class="table-wrap">
                                <table class="adm-table">
                                    <thead>
                                        <tr>
                                            <th>Sub-Topik</th>
                                            <th>Mata Uji</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topics as $tp)
                                            <tr>
                                                <td><strong>{{ $tp->name }}</strong><br><small
                                                        class="text-muted">{{ $tp->description }}</small></td>
                                                <td><span class="badge-tag">{{ $tp->subject_name }}</span></td>
                                                <td>
                                                    <button type="button" class="btn-xs btn-edit"
                                                        onclick='openTopicModal(@json($tp))'>✏️
                                                        Edit</button>
                                                    <button type="button" class="btn-xs btn-danger"
                                                        onclick="deleteRecord('topics', {{ $tp->id }}, 'tab-curriculum')">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==========================================================
                     TAB 6: CMS TAMPILAN BERANDA (`site_settings`, `landing_menus`, `landing_features`)
                     ========================================================== -->
                <section class="adm-pane" id="tab-cms">
                    <div class="adm-card mb-14">
                        <div class="adm-card-head">
                            <div>
                                <h3>1. Pengaturan Teks Beranda, Hero & Kontak Footer (`site_settings`)</h3>
                                <p>Klik <strong>✏️ Edit</strong> pada baris mana pun untuk mengubah teks melalui Pop-Up
                                    Modal.</p>
                            </div>
                            <button type="button" class="btn-primary" onclick="openSettingModal()">+ Tambah
                                Pengaturan Baru</button>
                        </div>
                        <div class="table-wrap">
                            <table class="adm-table">
                                <thead>
                                    <tr>
                                        <th>Grup</th>
                                        <th>Label & Key</th>
                                        <th>Isi Teks / Nilai</th>
                                        <th>Tampil?</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($siteSettings as $st)
                                        <tr>
                                            <td><span class="badge-tag">{{ $st->group }}</span></td>
                                            <td><strong>{{ $st->label }}</strong><br><small
                                                    class="text-muted">{{ $st->key }}</small></td>
                                            <td>{{ Str::limit($st->value, 85) }}</td>
                                            <td>
                                                <button type="button"
                                                    class="toggle-btn {{ $st->is_displayed ? 'on' : 'off' }}"
                                                    onclick="toggleStatus('site_settings', {{ $st->id }}, 'is_displayed', 'tab-cms')">
                                                    {{ $st->is_displayed ? 'Tampil' : 'Sembunyi' }}
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn-xs btn-edit"
                                                    onclick='openSettingModal(@json($st))'>✏️
                                                    Edit</button>
                                                <button type="button" class="btn-xs btn-danger"
                                                    onclick="deleteRecord('site_settings', {{ $st->id }}, 'tab-cms')">Hapus</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="adm-two-col">
                        <div class="adm-card">
                            <div class="adm-card-head">
                                <h3>2. Menu Navigasi (`landing_menus`)</h3>
                                <button type="button" class="btn-primary" onclick="openMenuModal()">+ Tambah
                                    Menu</button>
                            </div>
                            <div class="table-wrap">
                                <table class="adm-table">
                                    <thead>
                                        <tr>
                                            <th>Menu</th>
                                            <th>Lokasi</th>
                                            <th>Tampil</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($landingMenus as $mn)
                                            <tr>
                                                <td><strong>{{ $mn->title }}</strong><br><small>{{ $mn->url }}</small>
                                                </td>
                                                <td><span class="badge-tag">{{ $mn->location }}</span></td>
                                                <td>
                                                    <button type="button"
                                                        class="toggle-btn {{ $mn->is_displayed ? 'on' : 'off' }}"
                                                        onclick="toggleStatus('landing_menus', {{ $mn->id }}, 'is_displayed', 'tab-cms')">
                                                        {{ $mn->is_displayed ? 'Tampil' : 'Sembunyi' }}
                                                    </button>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn-xs btn-edit"
                                                        onclick='openMenuModal(@json($mn))'>✏️
                                                        Edit</button>
                                                    <button type="button" class="btn-xs btn-danger"
                                                        onclick="deleteRecord('landing_menus', {{ $mn->id }}, 'tab-cms')">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="adm-card">
                            <div class="adm-card-head">
                                <h3>3. Fitur Unggulan (`landing_features`)</h3>
                                <button type="button" class="btn-primary" onclick="openFeatureModal()">+ Tambah
                                    Fitur</button>
                            </div>
                            <div class="table-wrap">
                                <table class="adm-table">
                                    <thead>
                                        <tr>
                                            <th>Judul Fitur</th>
                                            <th>Tampil</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($landingFeatures as $ft)
                                            <tr>
                                                <td><strong>{{ $ft->title }}</strong><br><small
                                                        class="text-muted">{{ Str::limit($ft->description, 55) }}</small>
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="toggle-btn {{ $ft->is_displayed ? 'on' : 'off' }}"
                                                        onclick="toggleStatus('landing_features', {{ $ft->id }}, 'is_displayed', 'tab-cms')">
                                                        {{ $ft->is_displayed ? 'Tampil' : 'Sembunyi' }}
                                                    </button>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn-xs btn-edit"
                                                        onclick='openFeatureModal(@json($ft))'>✏️
                                                        Edit</button>
                                                    <button type="button" class="btn-xs btn-danger"
                                                        onclick="deleteRecord('landing_features', {{ $ft->id }}, 'tab-cms')">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==========================================================
                     TAB 7: MANAJEMEN PENGGUNA (`users`)
                     ========================================================== -->
                <section class="adm-pane" id="tab-users">
                    <div class="adm-card">
                        <div class="adm-card-head">
                            <h3>👥 Manajemen Pengguna: Admin, Mentor & Peserta (`users`)</h3>
                            <button type="button" class="btn-primary" onclick="openUserModal()">+ Tambah User
                                Baru</button>
                        </div>
                        <div class="table-wrap">
                            <table class="adm-table">
                                <thead>
                                    <tr>
                                        <th>ID & Nama</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Google</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $u)
                                        <tr>
                                            <td><strong>#{{ $u->id }} - {{ $u->name }}</strong></td>
                                            <td>{{ $u->email }}</td>
                                            <td><span
                                                    class="badge-tag role-{{ $u->role }}">{{ strtoupper($u->role) }}</span>
                                            </td>
                                            <td>{{ $u->google_id ? '✅ Terhubung' : '—' }}</td>
                                            <td>
                                                <button type="button" class="btn-xs btn-edit"
                                                    onclick='openUserModal(@json($u))'>✏️
                                                    Edit</button>
                                                @if ($u->id !== $admin->id)
                                                    <button type="button" class="btn-xs btn-danger"
                                                        onclick="deleteRecord('users', {{ $u->id }}, 'tab-users')">Hapus</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- ==========================================================
                     TAB 8: TRANSAKSI & HAK AKSES (`transactions` & `user_accesses`)
                     ========================================================== -->
                <section class="adm-pane" id="tab-transactions">
                    <div class="adm-two-col">
                        <div class="adm-card">
                            <div class="adm-card-head">
                                <h3>💳 Transaksi (`transactions`)</h3>
                                <button type="button" class="btn-primary" onclick="openTrxModal()">+ Tambah
                                    Transaksi</button>
                            </div>
                            <div class="table-wrap">
                                <table class="adm-table">
                                    <thead>
                                        <tr>
                                            <th>Invoice & Siswa</th>
                                            <th>Paket</th>
                                            <th>Nominal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $tx)
                                            <tr>
                                                <td><strong>{{ $tx->invoice_code }}</strong><br><small>{{ $tx->user_name }}</small>
                                                </td>
                                                <td>{{ Str::limit($tx->package_name, 28) }}</td>
                                                <td>Rp {{ number_format($tx->total_amount, 0, ',', '.') }}<br><span
                                                        class="badge-tag">{{ strtoupper($tx->payment_status) }}</span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn-xs btn-edit"
                                                        onclick='openTrxModal(@json($tx))'>✏️
                                                        Edit</button>
                                                    <button type="button" class="btn-xs btn-danger"
                                                        onclick="deleteRecord('transactions', {{ $tx->id }}, 'tab-transactions')">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="adm-card">
                            <div class="adm-card-head">
                                <h3>🎟️ Hak Akses Paket (`user_accesses`)</h3>
                                <button type="button" class="btn-primary" onclick="openAccessModal()">+ Beri
                                    Akses</button>
                            </div>
                            <div class="table-wrap">
                                <table class="adm-table">
                                    <thead>
                                        <tr>
                                            <th>Siswa</th>
                                            <th>Paket</th>
                                            <th>Berlaku Sampai</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($userAccesses as $ua)
                                            <tr>
                                                <td><strong>{{ $ua->user_name }}</strong></td>
                                                <td>{{ Str::limit($ua->package_name, 30) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($ua->expires_at)->format('d M Y') }}</td>
                                                <td>
                                                    <button type="button" class="btn-xs btn-edit"
                                                        onclick='openAccessModal(@json($ua))'>✏️
                                                        Edit</button>
                                                    <button type="button" class="btn-xs btn-danger"
                                                        onclick="deleteRecord('user_accesses', {{ $ua->id }}, 'tab-transactions')">Cabut</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==========================================================
                     TAB 9: HASIL UJIAN & DIAGNOSTIK
                     ========================================================== -->
                <section class="adm-pane" id="tab-results">
                    <div class="adm-card">
                        <div class="adm-card-head">
                            <h3>🎯 Rapor Competency Diagnostic Siswa (`competency_diagnostics`)</h3>
                            <button type="button" class="btn-primary" onclick="openDiagModal()">+ Tambah
                                Diagnostik</button>
                        </div>
                        <div class="table-wrap">
                            <table class="adm-table">
                                <thead>
                                    <tr>
                                        <th>Siswa</th>
                                        <th>Sub-Topik</th>
                                        <th>Benar/Dijawab</th>
                                        <th>Akurasi & Kecepatan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($diagnostics as $dg)
                                        <tr>
                                            <td><strong>{{ $dg->user_name }}</strong></td>
                                            <td>{{ $dg->topic_name }}</td>
                                            <td>{{ $dg->total_correct }} / {{ $dg->total_answered }}</td>
                                            <td>{{ $dg->accuracy_percentage }}% ({{ $dg->avg_time_seconds }}
                                                dtk/soal)</td>
                                            <td><span class="badge-tag">{{ $dg->status_label }}</span></td>
                                            <td>
                                                <button type="button" class="btn-xs btn-edit"
                                                    onclick='openDiagModal(@json($dg))'>✏️
                                                    Edit</button>
                                                <button type="button" class="btn-xs btn-danger"
                                                    onclick="deleteRecord('competency_diagnostics', {{ $dg->id }}, 'tab-results')">Hapus</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

            </main>
        </div>
    </div>

    <!-- ======================================================================
         KUMPULAN POP-UP MODAL UNTUK SEMUA OPERASI TAMBAH & EDIT
         ====================================================================== -->

    <!-- 1. MODAL MASTER PAKET TRYOUT -->
    <div class="adm-modal" id="modal-tryout">
        <div class="adm-modal-content">
            <div class="adm-modal-header">
                <h3 id="modal-tryout-title">Tambah Master Paket Tryout</h3>
                <button type="button" class="adm-modal-close" onclick="closeModal('modal-tryout')">&times;</button>
            </div>
            <form action="{{ route('admin.tryouts.save') }}" method="POST" class="adm-modal-body">
                @csrf
                <input type="hidden" name="id" id="to_id">
                <div class="form-grid-2">
                    <div>
                        <label class="adm-label">Kode Paket Tryout</label>
                        <input type="text" name="code" id="to_code" class="adm-input"
                            placeholder="TO-SKD-07" required>
                    </div>
                    <div>
                        <label class="adm-label">Program Kurikulum</label>
                        <select name="program_id" id="to_prog" class="adm-input">
                            @foreach ($programs as $pr)
                                <option value="{{ $pr->id }}">{{ $pr->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-10">
                    <label class="adm-label">Nama Master Paket Tryout</label>
                    <input type="text" name="title" id="to_title" class="adm-input"
                        placeholder="Paket Tryout 7: Simulasi..." required>
                </div>
                <div class="form-grid-3 mt-10">
                    <div>
                        <label class="adm-label">Tipe Ujian</label>
                        <select name="type" id="to_type" class="adm-input">
                            <option value="full_skd">Full SKD / STIS</option>
                            <option value="per_materi">Spesialisasi Materi</option>
                            <option value="psikologi_koran">Psikotes Koran</option>
                            <option value="hafal_denah">Psikotes Hafal Denah</option>
                        </select>
                    </div>
                    <div>
                        <label class="adm-label">Durasi (Menit)</label>
                        <input type="number" name="duration_minutes" id="to_dur" class="adm-input"
                            value="60" required>
                    </div>
                    <div>
                        <label class="adm-label">Target Kuota Soal (Fleksibel)</label>
                        <input type="number" name="total_questions" id="to_total" class="adm-input"
                            value="20" min="1" required>
                    </div>
                </div>
                <div class="mt-10">
                    <label class="adm-label">Instruksi Pengerjaan</label>
                    <textarea name="instruction" id="to_inst" rows="2" class="adm-input"
                        placeholder="Petunjuk pengerjaan tryout..."></textarea>
                </div>
                <div class="adm-modal-footer">
                    <label class="chk-label"><input type="checkbox" name="is_active" id="to_active" checked> Paket
                        Aktif</label>
                    <div class="flex-gap">
                        <button type="button" class="btn-outline"
                            onclick="closeModal('modal-tryout')">Batal</button>
                        <button type="submit" class="btn-primary">💾 Simpan Paket Tryout</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. MODAL BANK SOAL, GAMBAR SOAL, PEMBAHASAN & GAMBAR OPSI A-E (XL MODAL) -->
    <div class="adm-modal" id="modal-question">
        <div class="adm-modal-content modal-xl">
            <div class="adm-modal-header">
                <h3 id="modal-question-title">Tambah / Edit Butir Soal & Opsi Jawaban</h3>
                <button type="button" class="adm-modal-close"
                    onclick="closeModal('modal-question')">&times;</button>
            </div>
            <form action="{{ route('admin.questions.save') }}" method="POST" class="adm-modal-body">
                @csrf
                <input type="hidden" name="id" id="q_id">
                <div class="form-grid-4">
                    <div>
                        <label class="adm-label">Master Paket Tryout</label>
                        <select name="tryout_id" id="q_tryout" class="adm-input" required>
                            @foreach ($tryouts as $t)
                                <option value="{{ $t->id }}">[{{ $t->code }}]
                                    {{ Str::limit($t->title, 32) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="adm-label">Nomor Urut Soal (Bebas > 20)</label>
                        <input type="number" name="question_number" id="q_number" class="adm-input"
                            value="{{ $nextQuestionNumber }}" min="1" required>
                    </div>
                    <div>
                        <label class="adm-label">Sub-Topik Kompetensi</label>
                        <select name="topic_id" id="q_topic" class="adm-input" required>
                            @foreach ($topics as $tp)
                                <option value="{{ $tp->id }}">{{ $tp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="adm-label">Target Waktu Ideal (Detik)</label>
                        <input type="number" name="ideal_time_seconds" id="q_ideal" class="adm-input"
                            value="50" required>
                    </div>
                </div>

                <div class="form-grid-2 mt-10">
                    <div>
                        <label class="adm-label">Teks Pertanyaan Soal</label>
                        <textarea name="question_text" id="q_text" rows="3" class="adm-input"
                            placeholder="Tuliskan pertanyaan soal..." required></textarea>
                        <div class="mt-6">
                            <label class="adm-label">🖼️ Link Gambar Soal / Denah (Opsional)</label>
                            <input type="url" name="question_image" id="q_img" class="adm-input"
                                placeholder="https://placehold.co/720x320?text=Gambar+Soal">
                        </div>
                    </div>
                    <div>
                        <label class="adm-label">💡 Teks Pembahasan Lengkap</label>
                        <textarea name="discussion_text" id="q_disc" rows="3" class="adm-input"
                            placeholder="Tuliskan pembahasan & trik cepat..."></textarea>
                        <div class="form-grid-2 mt-6">
                            <div>
                                <label class="adm-label">🖼️ Link Gambar Pembahasan (Opsional)</label>
                                <input type="url" name="discussion_image" id="q_disc_img" class="adm-input"
                                    placeholder="https://placehold.co/720x300?text=Skema+Pembahasan">
                            </div>
                            <div>
                                <label class="adm-label">🎬 Link Video Pembahasan (Opsional)</label>
                                <input type="url" name="discussion_video_url" id="q_vid" class="adm-input"
                                    placeholder="https://youtube.com/...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pilihan Jawaban A-E Beserta Kolom Link Gambar Opsi Jawaban -->
                <div class="options-builder mt-10">
                    <label class="adm-label">Pilihan Jawaban A, B, C, D, E (Bisa diisi Teks, Link Gambar Opsi, atau
                        Keduanya):</label>
                    <div class="options-grid-5">
                        @foreach (['A', 'B', 'C', 'D', 'E'] as $lbl)
                            <div class="opt-item">
                                <div class="flex-between">
                                    <span class="opt-badge">Opsi {{ $lbl }}</span>
                                    <small class="text-muted">Bobot Poin:</small>
                                </div>
                                <input type="text" name="option_text_{{ $lbl }}"
                                    id="opt_text_{{ $lbl }}" class="adm-input"
                                    placeholder="Teks Jawaban {{ $lbl }}">
                                <input type="url" name="option_image_{{ $lbl }}"
                                    id="opt_img_{{ $lbl }}" class="adm-input"
                                    placeholder="🖼️ Link Gambar Opsi {{ $lbl }} (Opsional)">
                                <input type="number" name="score_weight_{{ $lbl }}"
                                    id="opt_weight_{{ $lbl }}" class="adm-input opt-score"
                                    value="{{ $lbl === 'B' ? 5 : 0 }}"
                                    title="Bobot Skor Opsi {{ $lbl }}">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="adm-modal-footer">
                    <small class="text-muted">Tips: Untuk soal Figural, masukkan Link Gambar pada kolom Gambar Opsi
                        A–E.</small>
                    <div class="flex-gap">
                        <button type="button" class="btn-outline"
                            onclick="closeModal('modal-question')">Batal</button>
                        <button type="submit" class="btn-primary">💾 Simpan Soal & Opsi Jawaban</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. MODAL PAKET BELAJAR & ISI BUNDLING (XL MODAL) -->
    <div class="adm-modal" id="modal-package">
        <div class="adm-modal-content modal-xl">
            <div class="adm-modal-header">
                <h3 id="modal-package-title">Rakit Paket Belajar & Pilih Isi Bundling</h3>
                <button type="button" class="adm-modal-close" onclick="closeModal('modal-package')">&times;</button>
            </div>
            <form action="{{ route('admin.packages.save') }}" method="POST" class="adm-modal-body">
                @csrf
                <input type="hidden" name="id" id="pkg_id">
                <div class="form-grid-4">
                    <div>
                        <label class="adm-label">Nama Paket</label>
                        <input type="text" name="name" id="pkg_name" class="adm-input" required>
                    </div>
                    <div>
                        <label class="adm-label">Kategori Paket</label>
                        <select name="type" id="pkg_type" class="adm-input">
                            <option value="bundling">Bundling (Tryout + Video)</option>
                            <option value="to_full">Paket Tryout Full</option>
                            <option value="to_materi">Mini TO Per Materi</option>
                            <option value="private_1on1">Private Mentoring 1-on-1</option>
                        </select>
                    </div>
                    <div>
                        <label class="adm-label">Badge Label Atas</label>
                        <input type="text" name="badge_text" id="pkg_badge" class="adm-input"
                            placeholder="Terlaris • Bundling">
                    </div>
                    <div>
                        <label class="adm-label">Harga Asli & Diskon (Rp)</label>
                        <div class="flex-gap">
                            <input type="number" name="price" id="pkg_price" class="adm-input"
                                placeholder="Normal" required>
                            <input type="number" name="discount_price" id="pkg_disc" class="adm-input"
                                placeholder="Diskon">
                        </div>
                    </div>
                </div>

                <div class="bundling-selector-grid mt-10">
                    <div class="bs-box">
                        <div class="bs-head">🗂️ Pilih Paket Tryout:</div>
                        <div class="bs-list">
                            @foreach ($tryouts as $to)
                                <label class="bs-item">
                                    <input type="checkbox" name="tryout_ids[]" value="{{ $to->id }}"
                                        class="chk-pkg-tryout">
                                    <span><strong>[{{ $to->code }}]</strong> {{ $to->title }}
                                        ({{ $to->actual_questions_count }} Soal)</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="bs-box">
                        <div class="bs-head">🎬 Pilih Video Materi / Modul:</div>
                        <div class="bs-list">
                            @foreach ($materials as $mat)
                                <label class="bs-item">
                                    <input type="checkbox" name="material_ids[]" value="{{ $mat->id }}"
                                        class="chk-pkg-material">
                                    <span><strong>[{{ strtoupper($mat->type) }}]</strong> {{ $mat->title }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="bs-box">
                        <div class="bs-head">👨‍🏫 Sesi Private 1-on-1:</div>
                        <div class="bs-list">
                            <label class="adm-label">Jumlah Sesi Live 1-on-1:</label>
                            <input type="number" name="private_sessions_count" id="pkg_priv_count"
                                class="adm-input mb-8" value="0" min="0">
                            @foreach ($users->where('role', 'instructor') as $mentor)
                                <label class="bs-item">
                                    <input type="checkbox" name="mentor_ids[]" value="{{ $mentor->id }}"
                                        class="chk-pkg-mentor">
                                    <span>Mentor: <strong>{{ $mentor->name }}</strong></span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-grid-2 mt-10">
                    <div>
                        <label class="adm-label">Deskripsi Singkat Paket</label>
                        <textarea name="short_description" id="pkg_desc" rows="3" class="adm-input"></textarea>
                        <div class="flex-gap mt-6">
                            <input type="number" name="active_period_days" id="pkg_days" class="adm-input"
                                value="365" placeholder="Masa Aktif (Hari)">
                            <input type="number" name="sort_order" id="pkg_sort" class="adm-input" value="1"
                                placeholder="Urutan">
                        </div>
                    </div>
                    <div>
                        <label class="adm-label">Checklist Fitur Etalase (1 Baris = 1 Fitur)</label>
                        <textarea name="features_text" id="pkg_features" rows="4" class="adm-input"></textarea>
                    </div>
                </div>

                <div class="adm-modal-footer">
                    <div class="flex-gap">
                        <label class="chk-label"><input type="checkbox" name="show_on_homepage" id="pkg_home"
                                checked> Tampil di Beranda</label>
                        <label class="chk-label"><input type="checkbox" name="is_featured" id="pkg_feat"> Highlight
                            Terlaris</label>
                    </div>
                    <div class="flex-gap">
                        <button type="button" class="btn-outline"
                            onclick="closeModal('modal-package')">Batal</button>
                        <button type="submit" class="btn-primary">💾 Simpan Paket Bundling</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. MODAL VIDEO MATERI -->
    <div class="adm-modal" id="modal-material">
        <div class="adm-modal-content">
            <div class="adm-modal-header">
                <h3 id="modal-material-title">Tambah / Edit Video Materi</h3>
                <button type="button" class="adm-modal-close"
                    onclick="closeModal('modal-material')">&times;</button>
            </div>
            <form action="{{ route('admin.materials.save') }}" method="POST" class="adm-modal-body">
                @csrf
                <input type="hidden" name="id" id="mat_id">
                <div class="form-grid-2">
                    <div>
                        <label class="adm-label">Sub-Topik</label>
                        <select name="topic_id" id="mat_topic" class="adm-input" required>
                            @foreach ($topics as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="adm-label">Pengajar / Mentor</label>
                        <select name="instructor_id" id="mat_inst" class="adm-input">
                            @foreach ($users->whereIn('role', ['instructor', 'admin']) as $inst)
                                <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-10">
                    <label class="adm-label">Judul Video / Modul</label>
                    <input type="text" name="title" id="mat_title" class="adm-input" required>
                </div>
                <div class="form-grid-2 mt-10">
                    <div>
                        <label class="adm-label">Tipe Konten</label>
                        <select name="type" id="mat_type" class="adm-input">
                            <option value="video">Video Pembelajaran</option>
                            <option value="pdf">Modul PDF</option>
                        </select>
                    </div>
                    <div>
                        <label class="adm-label">Durasi (Detik)</label>
                        <input type="number" name="duration" id="mat_dur" class="adm-input" value="900">
                    </div>
                </div>
                <div class="mt-10">
                    <label class="adm-label">URL Video / File</label>
                    <input type="url" name="content_url" id="mat_url" class="adm-input" required>
                </div>
                <div class="adm-modal-footer">
                    <label class="chk-label"><input type="checkbox" name="is_free_preview" id="mat_free"> Gratis
                        Preview</label>
                    <div class="flex-gap">
                        <button type="button" class="btn-outline"
                            onclick="closeModal('modal-material')">Batal</button>
                        <button type="submit" class="btn-primary">💾 Simpan Materi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 5. MODAL KURIKULUM: PROGRAM, SUBJECT, TOPIC -->
    <div class="adm-modal" id="modal-program">
        <div class="adm-modal-content">
            <div class="adm-modal-header">
                <h3 id="modal-program-title">Tambah / Edit Program</h3>
                <button type="button" class="adm-modal-close" onclick="closeModal('modal-program')">&times;</button>
            </div>
            <form action="{{ route('admin.programs.save') }}" method="POST" class="adm-modal-body">
                @csrf
                <input type="hidden" name="id" id="prog_id">
                <label class="adm-label">Nama Program</label>
                <input type="text" name="name" id="prog_name" class="adm-input" required>
                <div class="form-grid-2 mt-10">
                    <div>
                        <label class="adm-label">Badge Label</label>
                        <input type="text" name="badge_text" id="prog_badge" class="adm-input">
                    </div>
                    <div>
                        <label class="adm-label">Urutan Tampil</label>
                        <input type="number" name="sort_order" id="prog_sort" class="adm-input" value="1">
                    </div>
                </div>
                <div class="mt-10">
                    <label class="adm-label">Deskripsi Program</label>
                    <textarea name="description" id="prog_desc" rows="2" class="adm-input"></textarea>
                </div>
                <div class="adm-modal-footer">
                    <label class="chk-label"><input type="checkbox" name="show_on_homepage" id="prog_home" checked>
                        Tampil di Beranda</label>
                    <button type="submit" class="btn-primary">💾 Simpan Program</button>
                </div>
            </form>
        </div>
    </div>

    <div class="adm-modal" id="modal-subject">
        <div class="adm-modal-content">
            <div class="adm-modal-header">
                <h3 id="modal-subject-title">Tambah / Edit Mata Uji</h3>
                <button type="button" class="adm-modal-close" onclick="closeModal('modal-subject')">&times;</button>
            </div>
            <form action="{{ route('admin.subjects.save') }}" method="POST" class="adm-modal-body">
                @csrf
                <input type="hidden" name="id" id="subj_id">
                <label class="adm-label">Program</label>
                <select name="program_id" id="subj_prog" class="adm-input">
                    @foreach ($programs as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
                <div class="mt-10">
                    <label class="adm-label">Nama Mata Uji</label>
                    <input type="text" name="name" id="subj_name" class="adm-input" required>
                </div>
                <div class="mt-10">
                    <label class="adm-label">Ambang Batas (Passing Grade)</label>
                    <input type="number" name="passing_grade" id="subj_pg" class="adm-input" value="65"
                        required>
                </div>
                <div class="adm-modal-footer">
                    <label class="chk-label"><input type="checkbox" name="is_displayed" id="subj_disp" checked>
                        Tampilkan</label>
                    <button type="submit" class="btn-primary">💾 Simpan Mata Uji</button>
                </div>
            </form>
        </div>
    </div>

    <div class="adm-modal" id="modal-topic">
        <div class="adm-modal-content">
            <div class="adm-modal-header">
                <h3 id="modal-topic-title">Tambah / Edit Sub-Topik</h3>
                <button type="button" class="adm-modal-close" onclick="closeModal('modal-topic')">&times;</button>
            </div>
            <form action="{{ route('admin.topics.save') }}" method="POST" class="adm-modal-body">
                @csrf
                <input type="hidden" name="id" id="top_id">
                <label class="adm-label">Mata Uji</label>
                <select name="subject_id" id="top_subj" class="adm-input">
                    @foreach ($subjects as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
                <div class="mt-10">
                    <label class="adm-label">Nama Sub-Topik</label>
                    <input type="text" name="name" id="top_name" class="adm-input" required>
                </div>
                <div class="mt-10">
                    <label class="adm-label">Deskripsi Sub-Topik</label>
                    <textarea name="description" id="top_desc" rows="2" class="adm-input"></textarea>
                </div>
                <div class="adm-modal-footer">
                    <span></span>
                    <button type="submit" class="btn-primary">💾 Simpan Sub-Topik</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 6. MODAL CMS BERANDA: SETTING, MENU, FEATURE -->
    <div class="adm-modal" id="modal-setting">
        <div class="adm-modal-content">
            <div class="adm-modal-header">
                <h3 id="modal-setting-title">Tambah / Edit Pengaturan Beranda</h3>
                <button type="button" class="adm-modal-close" onclick="closeModal('modal-setting')">&times;</button>
            </div>
            <form action="{{ route('admin.settings.store') }}" method="POST" class="adm-modal-body">
                @csrf
                <input type="hidden" name="id" id="set_id">
                <div class="form-grid-2">
                    <div>
                        <label class="adm-label">Key Pengaturan</label>
                        <input type="text" name="key" id="set_key" class="adm-input" required>
                    </div>
                    <div>
                        <label class="adm-label">Grup</label>
                        <select name="group" id="set_group" class="adm-input">
                            <option value="hero">Hero Beranda</option>
                            <option value="section_headers">Judul Section</option>
                            <option value="footer_contact">Kontak Footer</option>
                            <option value="footer_social">Sosmed Footer</option>
                            <option value="general">General</option>
                        </select>
                    </div>
                </div>
                <div class="mt-10">
                    <label class="adm-label">Label</label>
                    <input type="text" name="label" id="set_label" class="adm-input" required>
                </div>
                <div class="mt-10">
                    <label class="adm-label">Isi Teks / Nilai</label>
                    <textarea name="value" id="set_value" rows="3" class="adm-input" required></textarea>
                </div>
                <div class="adm-modal-footer">
                    <label class="chk-label"><input type="checkbox" name="is_displayed" id="set_disp" checked>
                        Tampilkan</label>
                    <button type="submit" class="btn-primary">💾 Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="adm-modal" id="modal-menu">
        <div class="adm-modal-content">
            <div class="adm-modal-header">
                <h3 id="modal-menu-title">Tambah / Edit Menu Navigasi</h3>
                <button type="button" class="adm-modal-close" onclick="closeModal('modal-menu')">&times;</button>
            </div>
            <form action="{{ route('admin.menus.save') }}" method="POST" class="adm-modal-body">
                @csrf
                <input type="hidden" name="id" id="menu_id">
                <label class="adm-label">Judul Menu</label>
                <input type="text" name="title" id="menu_title" class="adm-input" required>
                <div class="form-grid-2 mt-10">
                    <div>
                        <label class="adm-label">URL / Anchor</label>
                        <input type="text" name="url" id="menu_url" class="adm-input" required>
                    </div>
                    <div>
                        <label class="adm-label">Lokasi</label>
                        <select name="location" id="menu_location" class="adm-input">
                            <option value="navbar">Navbar Atas</option>
                            <option value="footer_quicklink">Footer Quicklink</option>
                        </select>
                    </div>
                </div>
                <div class="adm-modal-footer">
                    <label class="chk-label"><input type="checkbox" name="is_displayed" id="menu_disp" checked>
                        Tampilkan</label>
                    <button type="submit" class="btn-primary">💾 Simpan Menu</button>
                </div>
            </form>
        </div>
    </div>

    <div class="adm-modal" id="modal-feature">
        <div class="adm-modal-content">
            <div class="adm-modal-header">
                <h3 id="modal-feature-title">Tambah / Edit Kartu Fitur Unggulan</h3>
                <button type="button" class="adm-modal-close" onclick="closeModal('modal-feature')">&times;</button>
            </div>
            <form action="{{ route('admin.features.save') }}" method="POST" class="adm-modal-body">
                @csrf
                <input type="hidden" name="id" id="feat_id">
                <label class="adm-label">Judul Fitur</label>
                <input type="text" name="title" id="feat_title" class="adm-input" required>
                <div class="form-grid-2 mt-10">
                    <div>
                        <label class="adm-label">Ikon</label>
                        <select name="icon_key" id="feat_icon" class="adm-input">
                            <option value="chart">Chart</option>
                            <option value="clock">Clock/Timer</option>
                            <option value="brain">Brain/Psikotes</option>
                            <option value="video">Video</option>
                            <option value="layers">Layers</option>
                            <option value="users">Users/Mentor</option>
                        </select>
                    </div>
                    <div>
                        <label class="adm-label">Warna Tema</label>
                        <select name="color_theme" id="feat_color" class="adm-input">
                            <option value="blue">Blue</option>
                            <option value="amber">Amber</option>
                            <option value="emerald">Emerald</option>
                            <option value="purple">Purple</option>
                            <option value="cyan">Cyan</option>
                            <option value="rose">Rose</option>
                        </select>
                    </div>
                </div>
                <div class="mt-10">
                    <label class="adm-label">Deskripsi Fitur</label>
                    <textarea name="description" id="feat_desc" rows="3" class="adm-input" required></textarea>
                </div>
                <div class="adm-modal-footer">
                    <label class="chk-label"><input type="checkbox" name="is_displayed" id="feat_disp" checked>
                        Tampilkan</label>
                    <button type="submit" class="btn-primary">💾 Simpan Fitur</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 7. MODAL USER, TRANSAKSI, AKSES & DIAGNOSTIK -->
    <div class="adm-modal" id="modal-user">
        <div class="adm-modal-content">
            <div class="adm-modal-header">
                <h3 id="modal-user-title">Tambah / Edit Pengguna</h3>
                <button type="button" class="adm-modal-close" onclick="closeModal('modal-user')">&times;</button>
            </div>
            <form action="{{ route('admin.users.save') }}" method="POST" class="adm-modal-body">
                @csrf
                <input type="hidden" name="id" id="usr_id">
                <label class="adm-label">Nama Lengkap</label>
                <input type="text" name="name" id="usr_name" class="adm-input" required>
                <div class="form-grid-2 mt-10">
                    <div>
                        <label class="adm-label">Email</label>
                        <input type="email" name="email" id="usr_email" class="adm-input" required>
                    </div>
                    <div>
                        <label class="adm-label">Role</label>
                        <select name="role" id="usr_role" class="adm-input">
                            <option value="student">Peserta (student)</option>
                            <option value="instructor">Mentor (instructor)</option>
                            <option value="admin">Admin (admin)</option>
                        </select>
                    </div>
                </div>
                <div class="mt-10">
                    <label class="adm-label">Password (Kosongkan jika tidak ingin mengubah password)</label>
                    <input type="password" name="password" class="adm-input"
                        placeholder="Default user baru: password123">
                </div>
                <div class="adm-modal-footer">
                    <span></span>
                    <button type="submit" class="btn-primary">💾 Simpan User</button>
                </div>
            </form>
        </div>
    </div>

    <div class="adm-modal" id="modal-trx">
        <div class="adm-modal-content">
            <div class="adm-modal-header">
                <h3 id="modal-trx-title">Tambah / Edit Transaksi</h3>
                <button type="button" class="adm-modal-close" onclick="closeModal('modal-trx')">&times;</button>
            </div>
            <form action="{{ route('admin.transactions.save') }}" method="POST" class="adm-modal-body">
                @csrf
                <input type="hidden" name="id" id="trx_id">
                <label class="adm-label">Pilih Siswa</label>
                <select name="user_id" id="trx_user" class="adm-input">
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
                <div class="mt-10">
                    <label class="adm-label">Paket Belajar</label>
                    <select name="package_id" id="trx_pkg" class="adm-input">
                        @foreach ($packages as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-grid-2 mt-10">
                    <div>
                        <label class="adm-label">Nominal Bayar (Rp)</label>
                        <input type="number" name="total_amount" id="trx_amt" class="adm-input" required>
                    </div>
                    <div>
                        <label class="adm-label">Status Pembayaran</label>
                        <select name="payment_status" id="trx_status" class="adm-input">
                            <option value="paid">PAID (Lunas & Buka Akses)</option>
                            <option value="pending">PENDING</option>
                            <option value="expired">EXPIRED</option>
                        </select>
                    </div>
                </div>
                <div class="adm-modal-footer">
                    <span></span>
                    <button type="submit" class="btn-primary">💾 Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    <div class="adm-modal" id="modal-access">
        <div class="adm-modal-content">
            <div class="adm-modal-header">
                <h3 id="modal-access-title">Tambah / Edit Hak Akses Paket</h3>
                <button type="button" class="adm-modal-close"
                    onclick="closeModal('modal-access')">&times;</button>
            </div>
            <form action="{{ route('admin.accesses.save') }}" method="POST" class="adm-modal-body">
                @csrf
                <input type="hidden" name="id" id="acc_id">
                <label class="adm-label">Siswa</label>
                <select name="user_id" id="acc_user" class="adm-input">
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
                <div class="mt-10">
                    <label class="adm-label">Paket</label>
                    <select name="package_id" id="acc_pkg" class="adm-input">
                        @foreach ($packages as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-10">
                    <label class="adm-label">Berlaku Sampai Tanggal</label>
                    <input type="date" name="expires_at" id="acc_exp" class="adm-input"
                        value="{{ now()->addYear()->format('Y-m-d') }}" required>
                </div>
                <div class="adm-modal-footer">
                    <span></span>
                    <button type="submit" class="btn-primary">💾 Simpan Akses</button>
                </div>
            </form>
        </div>
    </div>

    <div class="adm-modal" id="modal-diag">
        <div class="adm-modal-content">
            <div class="adm-modal-header">
                <h3 id="modal-diag-title">Tambah / Edit Rapor Competency Diagnostic</h3>
                <button type="button" class="adm-modal-close" onclick="closeModal('modal-diag')">&times;</button>
            </div>
            <form action="{{ route('admin.diagnostics.save') }}" method="POST" class="adm-modal-body">
                @csrf
                <input type="hidden" name="id" id="diag_id">
                <div class="form-grid-2">
                    <div>
                        <label class="adm-label">Siswa</label>
                        <select name="user_id" id="diag_user" class="adm-input">
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="adm-label">Sub-Topik</label>
                        <select name="topic_id" id="diag_topic" class="adm-input">
                            @foreach ($topics as $tp)
                                <option value="{{ $tp->id }}">{{ $tp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-grid-3 mt-10">
                    <div>
                        <label class="adm-label">Total Dijawab</label>
                        <input type="number" name="total_answered" id="diag_ans" class="adm-input"
                            value="20" required>
                    </div>
                    <div>
                        <label class="adm-label">Total Benar</label>
                        <input type="number" name="total_correct" id="diag_cor" class="adm-input"
                            value="15" required>
                    </div>
                    <div>
                        <label class="adm-label">Total Waktu (Detik)</label>
                        <input type="number" name="total_time_seconds" id="diag_time" class="adm-input"
                            value="800" required>
                    </div>
                </div>
                <div class="mt-10">
                    <label class="adm-label">Status Kompetensi</label>
                    <select name="status_label" id="diag_status" class="adm-input">
                        <option value="Unggul">Unggul (≥75%)</option>
                        <option value="Cukup">Cukup (50-74%)</option>
                        <option value="Lemah">Lemah (&lt;50%)</option>
                    </select>
                </div>
                <div class="adm-modal-footer">
                    <span></span>
                    <button type="submit" class="btn-primary">💾 Simpan Diagnostik</button>
                </div>
            </form>
        </div>
    </div>

    <!-- BOTTOM NAV MOBILE -->
    <nav class="adm-mobile-nav">
        <button type="button" class="amn-btn active" data-tab="tab-overview">📊<span>Ringkasan</span></button>
        <button type="button" class="amn-btn" data-tab="tab-tryouts">🗂️<span>Tryout</span></button>
        <button type="button" class="amn-btn" data-tab="tab-questions">❓<span>Soal</span></button>
        <button type="button" class="amn-btn" data-tab="tab-packages">📦<span>Bundling</span></button>
        <button type="button" class="amn-btn" data-tab="tab-users">👥<span>User</span></button>
    </nav>

    <form id="universal-toggle-form" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="active_tab" id="toggle-active-tab">
    </form>
    <form id="universal-delete-form" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
        <input type="hidden" name="active_tab" id="delete-active-tab">
    </form>

    <script src="{{ asset('dashboard/admin/js/admin.js') }}"></script>
</body>

</html>
