@extends('dashboard.layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
    <!-- Notifikasi Pengguna Google yang Belum Mengatur Password -->
    @if (empty($user->password))
        <div class="password-prompt-bar">
            <div class="ppb-left">
                <span class="ppb-icon">💡</span>
                <div>
                    <strong>Anda masuk menggunakan akun Google.</strong>
                    <span>Atur password sekarang agar bisa login langsung menggunakan Email & Password.</span>
                </div>
            </div>
            <button type="button" class="btn-sm-primary" data-switch-tab="tab-settings">Atur Password</button>
        </div>
    @endif

    <!-- Panel Khusus ADMIN atau MENTOR -->
    @if ($user->role === 'admin' && $roleStats)
        <div class="role-special-banner">
            <h4>🛡️ Panel Ringkasan Administrator</h4>
            <div class="role-stats-row">
                <div><small>Total Peserta</small><strong>{{ $roleStats['total_students'] }} Siswa</strong></div>
                <div><small>Total Mentor</small><strong>{{ $roleStats['total_mentors'] }} Pengajar</strong></div>
                <div><small>Total Sesi Tryout</small><strong>{{ $roleStats['total_attempts'] }} Sesi</strong></div>
                <div><small>Total Pendapatan Lunas</small><strong>Rp
                        {{ number_format($roleStats['total_revenue'], 0, ',', '.') }}</strong></div>
            </div>
        </div>
    @elseif($user->role === 'instructor' && $roleStats)
        <div class="role-special-banner">
            <h4>👨‍🏫 Panel Ringkasan Mentor / Pengajar</h4>
            <div class="role-stats-row">
                <div><small>Video Materi Saya</small><strong>{{ $roleStats['my_materials_count'] }} Modul/Video</strong>
                </div>
                <div><small>Total Ditonton Peserta</small><strong>{{ $roleStats['total_views'] }} Kali Ditonton</strong>
                </div>
            </div>
        </div>
    @endif

    <!-- ==========================================================
             TAB 1: OVERVIEW (RINGKASAN & PROGRES)
             ========================================================== -->
    <section class="tab-pane active" id="tab-overview">
        <div class="welcome-banner">
            <div class="wb-content">
                <span class="wb-badge">🔥 Smart Analytics CBT Engine</span>
                <h2>Pantau Evaluasi Per Tryout & Kecepatan Menjawabmu!</h2>
                @if ($attemptsHistory->count() > 0)
                    <p>Anda telah menyelesaikan <strong>{{ $attemptsHistory->count() }} sesi tryout</strong>. Klik tombol
                        analisis untuk membedah rapor sub-topik dan waktu per butir soal dari setiap tryout yang sudah Anda
                        kerjakan.</p>
                @else
                    <p>Akun Anda belum memiliki riwayat pengerjaan tryout. Yuk mulai simulasi tryout pertama Anda untuk
                        membuka analisis diagnostik otomatis!</p>
                @endif
                <div class="wb-actions">
                    <button type="button" class="btn-white" data-switch-tab="tab-tryout">🚀 Mulai Kerjakan Tryout</button>
                    <button type="button" class="btn-glass" data-switch-tab="tab-diagnostic">📊 Analisis Hasil
                        Tryout</button>
                </div>
            </div>
            <div class="wb-visual">
                <div class="score-circle">
                    <span class="sc-val">{{ $diagSummary['avg_accuracy'] }}%</span>
                    <span class="sc-lbl">Akurasi Rata-rata</span>
                </div>
            </div>
        </div>

        <!-- Bento KPI Cards -->
        <div class="stats-grid">
            <div class="stat-card" data-switch-tab="tab-packages">
                <div class="sc-icon icon-blue">📦</div>
                <div>
                    <span class="sc-title">Paket Aktif Saya</span>
                    <h3 class="sc-value">{{ $myAccesses->where('is_active', true)->count() }} <small>Paket</small></h3>
                </div>
            </div>
            <div class="stat-card" data-switch-tab="tab-diagnostic">
                <div class="sc-icon icon-emerald">🏆</div>
                <div>
                    <span class="sc-title">Topik Status Unggul</span>
                    <h3 class="sc-value">{{ $diagSummary['unggul'] }} <small>Topik</small></h3>
                </div>
            </div>
            <div class="stat-card" data-switch-tab="tab-diagnostic">
                <div class="sc-icon icon-amber">⏱️</div>
                <div>
                    <span class="sc-title">Rata-rata Kecepatan</span>
                    <h3 class="sc-value">{{ $diagSummary['avg_speed'] }} <small>dtk/soal</small></h3>
                </div>
            </div>
            <div class="stat-card" data-switch-tab="tab-diagnostic">
                <div class="sc-icon icon-rose">📝</div>
                <div>
                    <span class="sc-title">Tryout Diselesaikan</span>
                    <h3 class="sc-value">{{ $attemptsHistory->count() }} <small>Sesi</small></h3>
                </div>
            </div>
        </div>

        <div class="overview-two-col">
            <!-- Kolom Kiri: Daftar Hasil Tryout Terakhir (Klik untuk Analisis) -->
            <div class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">🎯 Analisis Hasil Per Tryout Terakhir</h3>
                        <p class="panel-subtitle">Pilih sesi tryout untuk melihat analisis sub-topik & waktu per soal</p>
                    </div>
                    @if ($attemptsHistory->count() > 0)
                        <button type="button" class="btn-link" data-switch-tab="tab-diagnostic">Semua Sesi &rarr;</button>
                    @endif
                </div>

                @if ($attemptsHistory->count() > 0)
                    <div class="attempt-card-list">
                        @foreach ($attemptsHistory->take(4) as $att)
                            <div class="attempt-item-card">
                                <div class="aic-left">
                                    <div class="aic-badges">
                                        <span
                                            class="subj-tag">{{ strtoupper(str_replace('_', ' ', $att->tryout_type ?? 'TRYOUT')) }}</span>
                                        <span class="status-badge {{ $att->is_passed ? 'badge-unggul' : 'badge-lemah' }}">
                                            {{ $att->is_passed ? 'LULUS PG' : 'BELUM LULUS' }}
                                        </span>
                                    </div>
                                    <h4 class="aic-title">{{ $att->tryout_title }}</h4>
                                    <div class="aic-meta">
                                        <span>🏆 Skor: <strong>{{ $att->total_score }} Poin</strong></span>
                                        <span>•</span>
                                        <span>⏱️ {{ $att->avg_speed }} dtk/soal</span>
                                        @if ($att->finished_at)
                                            <span>•</span>
                                            <span>📅
                                                {{ \Carbon\Carbon::parse($att->finished_at)->format('d M Y, H:i') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="aic-right">
                                    <a href="{{ route('dashboard.analysis', $att->id) }}"
                                        class="btn-primary btn-sm-action">
                                        📊 Lihat Analisis
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state-box">
                        <div class="es-icon">📭</div>
                        <h4>Belum Ada Riwayat Tryout</h4>
                        <p>Kerjakan simulasi Tryout pertama Anda untuk melihat rapor analisis mendalam per tryout.</p>
                        <button type="button" class="btn-primary" data-switch-tab="tab-tryout">Kerjakan Tryout
                            Sekarang</button>
                    </div>
                @endif
            </div>

            <!-- Kolom Kanan: Paket Aktif & Ringkasan Kompetensi Global -->
            <div class="side-stack">
                <div class="panel-card">
                    <div class="panel-header">
                        <h3 class="panel-title">🎟️ Paket Aktif Saya ({{ $myAccesses->count() }})</h3>
                        <button type="button" class="btn-link" data-switch-tab="tab-packages">+ Beli Paket</button>
                    </div>
                    @if ($myAccesses->count() > 0)
                        <div class="pkg-mini-stack">
                            @foreach ($myAccesses as $acc)
                                <div class="my-pkg-mini">
                                    <div class="mpm-top">
                                        <span class="mpm-badge">{{ $acc->badge_text ?? 'Paket Aktif' }}</span>
                                        <span class="mpm-days">Sisa {{ $acc->days_remaining }} Hari</span>
                                    </div>
                                    <h4>{{ $acc->package_name }}</h4>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state-box compact">
                            <p>Anda belum memiliki paket belajar aktif.</p>
                            <button type="button" class="btn-primary" data-switch-tab="tab-packages">Pilih Paket
                                Belajar</button>
                        </div>
                    @endif
                </div>

                <div class="panel-card">
                    <div class="panel-header">
                        <div>
                            <h3 class="panel-title">📈 Peta Sub-Topik Keseluruhan</h3>
                            <p class="panel-subtitle">Akumulasi dari seluruh tryout Anda</p>
                        </div>
                    </div>
                    @if ($diagnostics->count() > 0)
                        <div class="diag-list">
                            @foreach ($diagnostics->take(3) as $diag)
                                @php $stClass = strtolower($diag->status_label); @endphp
                                <div class="diag-row">
                                    <div class="diag-row-top">
                                        <div>
                                            <span class="subj-tag">{{ $diag->subject_name }}</span>
                                            <h4 class="diag-topic-title">{{ $diag->topic_name }}</h4>
                                        </div>
                                        <span class="status-badge badge-{{ $stClass }}">
                                            {{ $diag->status_label }}
                                            ({{ number_format($diag->accuracy_percentage, 0) }}%)
                                        </span>
                                    </div>
                                    <div class="progress-bar-bg">
                                        <div class="progress-bar-fill fill-{{ $stClass }}"
                                            style="width: {{ $diag->accuracy_percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted-sm">Data pemetaan topik akan muncul otomatis setelah Anda mengerjakan tryout.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- ==========================================================
             TAB 2: ANALISIS HASIL PER TRYOUT (DAFTAR SESI TRYOUT USER)
             ========================================================== -->
    <section class="tab-pane" id="tab-diagnostic">
        <div class="section-head-flex">
            <span class="section-tag">Evaluasi Cerdas Per Sesi Ujian</span>
            <h2>Daftar Hasil & Analisis Per Tryout Anda</h2>
            <p>Pilih salah satu sesi tryout yang telah Anda kerjakan di bawah ini untuk membuka halaman penuh analisis
                sub-topik, rekomendasi materi, dan pelacakan waktu per butir soal.</p>
        </div>

        @if ($attemptsHistory->count() > 0)
            <div class="tryout-grid">
                @foreach ($attemptsHistory as $index => $att)
                    <div class="tryout-card attempt-analysis-card">
                        <div>
                            <div class="tc-top">
                                <span class="subj-tag">SESI #{{ $attemptsHistory->count() - $index }} •
                                    {{ strtoupper(str_replace('_', ' ', $att->tryout_type ?? 'CBT')) }}</span>
                                <span class="status-badge {{ $att->is_passed ? 'badge-unggul' : 'badge-lemah' }}">
                                    {{ $att->is_passed ? '✅ LULUS PG' : '⚠️ BELUM LULUS' }}
                                </span>
                            </div>

                            <h3 class="tc-title">{{ $att->tryout_title }}</h3>
                            <p class="tc-inst">
                                Dikerjakan pada:
                                <strong>{{ $att->finished_at ? \Carbon\Carbon::parse($att->finished_at)->format('d M Y • H:i') : 'Selesai' }}</strong>
                            </p>
                        </div>

                        <div>
                            <div class="tc-specs">
                                <div class="tcs-item">
                                    <span>Skor Akhir</span>
                                    <strong class="text-primary-bold">{{ $att->total_score }} Poin</strong>
                                </div>
                                <div class="tcs-item">
                                    <span>Kecepatan Rata-rata</span>
                                    <strong>⏱️ {{ $att->avg_speed }} dtk/soal</strong>
                                </div>
                            </div>

                            <a href="{{ route('dashboard.analysis', $att->id) }}" class="btn-primary btn-block"
                                style="text-decoration: none;">
                                🔍 Buka Analisis Detail Tryout Ini &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="panel-card">
                <div class="empty-state-box">
                    <div class="es-icon">📊</div>
                    <h4>Belum Ada Tryout yang Diselesaikan</h4>
                    <p>Anda belum menyelesaikan sesi Tryout apa pun. Silakan kerjakan Tryout terlebih dahulu agar sistem
                        dapat menyajikan rapor analisis per tryout.</p>
                    <button type="button" class="btn-primary" data-switch-tab="tab-tryout">Mulai Tryout Pertama</button>
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
                            <span class="subj-tag">{{ strtoupper(str_replace('_', ' ', $to->type)) }}</span>
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

                        <div class="tc-btn-group">
                            <a href="{{ route('cat.show', $to->id) }}" class="btn-primary btn-block"
                                style="text-decoration: none;">
                                🚀 {{ $to->user_attempts_count > 0 ? 'Kerjakan Ulang' : 'Mulai Kerjakan' }}
                            </a>
                            @if ($to->latest_attempt_id)
                                <a href="{{ route('dashboard.analysis', $to->latest_attempt_id) }}"
                                    class="btn-outline btn-block" style="text-decoration: none;">
                                    📊 Analisis Hasil Terakhir
                                </a>
                            @endif
                        </div>
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
                                <div class="progress-bar-fill fill-unggul" style="width: {{ $mat->progress_percent }}%">
                                </div>
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
                    <p class="tc-inst">Simulasi penjumlahan deret angka vertikal dengan perpindahan kolom otomatis untuk
                        mengukur kecepatan dan konsistensi kerja.</p>
                </div>
                <button type="button" class="btn-primary btn-block trigger-info-modal" data-title="Simulator Tes Koran"
                    data-msg="Membuka lembar lajur angka Tes Koran interaktif.">Mulai Tes Koran</button>
            </div>

            <div class="psiko-card">
                <div>
                    <div class="sc-icon icon-amber" style="margin-bottom:10px;">⚡</div>
                    <span class="subj-tag">Fokus & Refleks Cepat</span>
                    <h3 class="tc-title">Tes Ganjil Genap Cepat</h3>
                    <p class="tc-inst">Uji refleks kognitif dan akurasi klasifikasi kombinasi angka ganjil-genap dalam
                        tekanan hitung mundur detik.</p>
                </div>
                <button type="button" class="btn-primary btn-block trigger-info-modal"
                    data-title="Simulator Ganjil Genap" data-msg="Membuka simulator kecepatan Ganjil Genap.">Mulai Ganjil
                    Genap</button>
            </div>

            <div class="psiko-card">
                <div>
                    <div class="sc-icon icon-emerald" style="margin-bottom:10px;">🗺️</div>
                    <span class="subj-tag">Memorisasi 2 Fase</span>
                    <h3 class="tc-title">Tes Hafal Denah & Arah</h3>
                    <p class="tc-inst">Fase 1 menghafal letak gedung pada peta, lalu Fase 2 peta disembunyikan otomatis
                        saat Anda menjawab soal arah lokasi.</p>
                </div>
                <button type="button" class="btn-primary btn-block trigger-info-modal"
                    data-title="Simulator 2 Fase: Hafal Denah"
                    data-msg="Fase 1 (Memorisasi Peta Denah) siap dimulai.">Mulai Hafal Denah</button>
            </div>
        </div>
    </section>

    <!-- ==========================================================
             TAB 6: KATALOG PAKET BELAJAR
             ========================================================== -->
    <section class="tab-pane" id="tab-packages">
        <div class="section-head-flex">
            <span class="section-tag">Katalog Lengkap ({{ $catalogPackages->count() }} Pilihan Paket)</span>
            <h2>Pilih Paket Belajar SKD, STIS & Tes Psikologi</h2>
            <p>Tersedia paket Bundling Lengkap, Paket Khusus Tes Psikologi, TO Full, Mini TO per Materi, hingga Private
                1-on-1.</p>
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
                                <span class="price-old">Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                                <div class="price-now">Rp {{ number_format($pkg->discount_price, 0, ',', '.') }}</div>
                            @else
                                <div class="price-now">Rp {{ number_format($pkg->price, 0, ',', '.') }}</div>
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
                        <button type="button" class="btn-owned" disabled>✅ Sudah Aktif di Akun Anda</button>
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
                <p class="text-muted-sm">Belum ada riwayat pembelian paket pada akun ini.</p>
            @endif
        </div>
    </section>

    <!-- ==========================================================
             TAB 7: PENGATURAN AKUN & PASSWORD
             ========================================================== -->
    <section class="tab-pane" id="tab-settings">
        <div class="section-head-flex">
            <span class="section-tag">Keamanan & Kredensial</span>
            <h2>Pengaturan Akun & Password Login</h2>
            <p>Atur atau perbarui password Anda agar dapat login menggunakan Email & Password selain menggunakan Google.</p>
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
                        <small class="text-muted-xs">Nama Lengkap</small>
                        <div class="fw-bold-sm">{{ $user->name }}</div>
                    </div>
                    <div class="diag-row">
                        <small class="text-muted-xs">Alamat Email</small>
                        <div class="fw-bold-sm">{{ $user->email }}</div>
                    </div>
                    <div class="diag-row">
                        <small class="text-muted-xs">Status Login Password</small>
                        <div style="margin-top:4px;">
                            @if (empty($user->password))
                                <span class="status-badge badge-cukup">Belum Diatur (Hanya Login Google)</span>
                            @else
                                <span class="status-badge badge-unggul">Sudah Aktif (Bisa Login Email & Password)</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
