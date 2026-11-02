<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CAT Ujian: {{ $tryout->title }} - Forecasting Academy</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('dashboard/css/cat-exam.css') }}">
</head>
<body>

    <!-- =====================================================================
         TAHAP 1: HALAMAN PENJELASAN / BRIEFING TRYOUT SEBELUM MULAI
         ===================================================================== -->
    <div id="screen-briefing" class="briefing-wrapper">
        <div class="briefing-container">
            <!-- Header Navigasi Kembali -->
            <div class="briefing-topbar">
                <a href="{{ url('/dashboard') }}" class="btn-back-dash">
                    ← Kembali ke Dashboard Peserta
                </a>
                <div class="brand-pill">
                    <span class="dot-live"></span> COMPUTER ASSISTED TEST (CAT) — FORECASTING ACADEMY
                </div>
            </div>

            <!-- Kartu Utama Penjelasan Tryout -->
            <div class="briefing-card">
                <div class="bc-header">
                    <div>
                        <span class="bc-badge">{{ $tryout->program_name ?? 'Program Kedinasan & SKD' }} • Kode: {{ $tryout->code ?? 'TO-CAT' }}</span>
                        <h1 class="bc-title">{{ $tryout->title }}</h1>
                        <p class="bc-subtitle">Harap baca seluruh informasi dan petunjuk pengerjaan tryout di bawah ini dengan seksama sebelum menekan tombol mulai.</p>
                    </div>
                    <div class="bc-emblem">
                        <div class="cat-badge-logo">
                            <span>SISTEM</span>
                            <strong>CAT</strong>
                            <small>STANDAR BKN</small>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Data Peserta & Spesifikasi Ujian -->
                <div class="bc-info-grid">
                    <div class="info-box">
                        <span class="ib-label">Nomor Peserta</span>
                        <strong class="ib-val">{{ 1000 + ($user->id ?? 1) }}</strong>
                    </div>
                    <div class="info-box">
                        <span class="ib-label">Nama Peserta Ujian</span>
                        <strong class="ib-val">{{ strtoupper($user->name ?? 'PESERTA UJIAN') }}</strong>
                    </div>
                    <div class="info-box highlight-blue">
                        <span class="ib-label">Batas Waktu Ujian</span>
                        <strong class="ib-val">{{ $tryout->duration_minutes ?? 30 }} Menit</strong>
                    </div>
                    <div class="info-box highlight-cyan">
                        <span class="ib-label">Total Jumlah Soal</span>
                        <strong class="ib-val">{{ $tryout->total_questions ?? 40 }} Butir Soal</strong>
                    </div>
                </div>

                <!-- Tabel Struktur Kategori & Passing Grade (Jika ada di tryout_category_rules) -->
                @if($rules->count() > 0)
                <div class="bc-section">
                    <h3 class="sec-heading">📊 Komposisi Kategori Soal & Nilai Ambang Batas (Passing Grade)</h3>
                    <div class="table-wrap">
                        <table class="briefing-table">
                            <thead>
                                <tr>
                                    <th>Urutan Pengerjaan</th>
                                    <th>Kategori Materi</th>
                                    <th>Jumlah Soal Tampil</th>
                                    <th>Passing Grade</th>
                                    <th>Sistem Pengacakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rules as $idx => $rule)
                                <tr>
                                    <td><span class="order-pill">Bagian {{ $idx + 1 }}</span></td>
                                    <td><strong>{{ $rule->category }}</strong></td>
                                    <td><strong>{{ $rule->display_quota }} Soal</strong></td>
                                    <td><span class="pg-badge">{{ $rule->passing_grade }} Poin</span></td>
                                    <td>{{ ($rule->is_randomized ?? true) ? '🔀 Soal Diacak Otomatis' : '📋 Berurutan' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Petunjuk & Tata Tertib Pengerjaan CAT -->
                <div class="bc-section">
                    <h3 class="sec-heading">📌 Petunjuk & Mekanisme Pengerjaan CAT</h3>
                    <div class="rules-grid">
                        <div class="rule-item">
                            <div class="ri-icon bg-emerald">1</div>
                            <div>
                                <h4>Tombol "Simpan dan Lanjutkan"</h4>
                                <p>Pilih salah satu opsi jawaban (<strong>A, B, C, D, atau E</strong>), lalu klik tombol <strong>Simpan dan Lanjutkan</strong> untuk mengunci jawaban. Kotak nomor soal di sebelah kanan akan berubah menjadi <strong class="txt-green">HIJAU</strong> beserta huruf jawaban Anda.</p>
                            </div>
                        </div>
                        <div class="rule-item">
                            <div class="ri-icon bg-rose">2</div>
                            <div>
                                <h4>Tombol "Lewatkan soal ini"</h4>
                                <p>Jika Anda belum yakin atau ingin melewati soal, klik <strong>Lewatkan soal ini</strong>. Kotak nomor soal akan tetap berwarna <strong class="txt-red">MERAH (Belum Dijawab)</strong> dan Anda dapat kembali ke nomor tersebut kapan saja.</p>
                            </div>
                        </div>
                        <div class="rule-item">
                            <div class="ri-icon bg-blue">3</div>
                            <div>
                                <h4>Analisis Kecepatan per Soal (Diagnostic)</h4>
                                <p>Sistem mencatat waktu yang Anda habiskan pada setiap butir soal secara otomatis untuk memetakan sub-topik mana yang <strong>Unggul</strong>, <strong>Cukup</strong>, atau <strong>Lemah</strong>.</p>
                            </div>
                        </div>
                        <div class="rule-item">
                            <div class="ri-icon bg-amber">4</div>
                            <div>
                                <h4>Perhitungan Waktu Mundur</h4>
                                <p>Waktu ujian langsung berjalan setelah Anda menekan konfirmasi mulai. Jika waktu mencapai <strong>00:00:00</strong>, sistem otomatis menyimpan seluruh jawaban dan mengakhiri sesi ujian.</p>
                            </div>
                        </div>
                    </div>

                    @if(!empty($tryout->instruction))
                    <div class="custom-instruction-box">
                        <strong>Catatan Khusus Paket Tryout Ini:</strong>
                        <p>{{ $tryout->instruction }}</p>
                    </div>
                    @endif
                </div>

                <!-- Footer Action Briefing -->
                <div class="bc-footer">
                    <div class="status-ready-note">
                        @if($activeAttempt)
                            ⚠️ <span>Anda memiliki sesi ujian yang sedang berjalan. Klik tombol di samping untuk melanjutkan sisa waktu Anda.</span>
                        @else
                            ✅ <span>Pastikan koneksi internet stabil sebelum memulai simulasi CAT.</span>
                        @endif
                    </div>
                    <button type="button" class="btn-start-tryout" onclick="openConfirmStartModal()">
                        {{ $activeAttempt ? '▶️ Lanjutkan Ujian Sekarang' : '🚀 Mulai Kerjakan Tryout' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- =====================================================================
         TAHAP 2: POP-UP MODAL KONFIRMASI MULAI UJIAN
         ===================================================================== -->
    <div id="modal-confirm-start" class="cat-modal-overlay">
        <div class="cat-modal-box">
            <div class="cm-icon">⏱️</div>
            <h2 class="cm-title">Konfirmasi Mulai Ujian CAT</h2>
            <p class="cm-desc">
                Apakah Anda yakin ingin memulai <strong>{{ $tryout->title }}</strong> sekarang?
                Begitu Anda menekan tombol <strong>"Ya, Mulai Sekarang"</strong>, waktu hitung mundur selama <strong>{{ $tryout->duration_minutes ?? 30 }} menit</strong> akan langsung berjalan.
            </p>
            <div class="cm-summary">
                <div><span>Peserta:</span> <strong>{{ strtoupper($user->name ?? 'PESERTA') }} ({{ 1000 + ($user->id ?? 1) }})</strong></div>
                <div><span>Target Soal:</span> <strong>{{ $tryout->total_questions ?? 40 }} Butir Soal</strong></div>
                <div><span>Batas Waktu:</span> <strong>{{ $tryout->duration_minutes ?? 30 }} Menit</strong></div>
            </div>
            <label class="cm-checkbox">
                <input type="checkbox" id="chk-ready" onchange="toggleStartButton()">
                <span>Saya sudah membaca petunjuk dan siap mengerjakan ujian dengan jujur.</span>
            </label>
            <div class="cm-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeConfirmStartModal()">Batal</button>
                <button type="button" id="btn-confirm-go" class="btn-modal-confirm" disabled onclick="launchCatEngine()">
                    🔥 Ya, Mulai Ujian Sekarang!
                </button>
            </div>
        </div>
    </div>

    <!-- =====================================================================
         TAHAP 3: HALAMAN UTAMA MESIN UJIAN CAT (LAYOUT SESUAI GAMBAR A, B, C)
         ===================================================================== -->
    <div id="screen-cat-exam" class="cat-exam-wrapper" style="display: none;">

        <!-- ===============================================================
             BAGIAN A: HEADER BAR INFORMASI PESERTA, SKOR DIJAWAB, & TIMER
             =============================================================== -->
        <header class="cat-header-bar">
            <!-- Logo Bulat CAT di Kiri Atas -->
            <div class="cat-brand-left">
                <div class="cat-circle-emblem">
                    <span class="cce-top">COMPUTER ASSISTED TEST</span>
                    <strong class="cce-mid">CAT</strong>
                    <span class="cce-bot">FORECASTING</span>
                </div>
                <div class="cat-system-title">
                    <span class="cst-main">Computer Assisted Test</span>
                    <small class="cst-sub">{{ $tryout->title }}</small>
                </div>
            </div>

            <!-- Panel Statistik & Info Peserta (Kotak A pada Gambar) -->
            <div class="cat-info-strip">
                <div class="cis-col">
                    <span class="cis-label">Nomor Peserta</span>
                    <strong class="cis-val" id="hdr-participant-no">{{ 1000 + ($user->id ?? 1) }}</strong>
                </div>
                <div class="cis-col">
                    <span class="cis-label">Nama Peserta Ujian</span>
                    <strong class="cis-val" id="hdr-participant-name">{{ strtoupper($user->name ?? 'PESERTA 1002') }}</strong>
                </div>
                <div class="cis-col">
                    <span class="cis-label">Batas Waktu</span>
                    <strong class="cis-val" id="hdr-duration">{{ $tryout->duration_minutes ?? 30 }}</strong>
                </div>
                <div class="cis-col">
                    <span class="cis-label">Jumlah Soal</span>
                    <strong class="cis-val" id="hdr-total-q">0</strong>
                </div>
                <div class="cis-col">
                    <span class="cis-label txt-green">Soal Dijawab</span>
                    <strong class="cis-val txt-green" id="hdr-answered-q">0</strong>
                </div>
                <div class="cis-col">
                    <span class="cis-label txt-red">Belum Dijawab</span>
                    <strong class="cis-val txt-red" id="hdr-unanswered-q">0</strong>
                </div>
                <div class="cis-action-timer">
                    <button type="button" class="btn-finish-top" onclick="openFinishModal()">
                        Selesai Ujian
                    </button>
                    <div class="cat-digital-timer" id="cat-countdown-box">
                        00:00:00
                    </div>
                </div>
            </div>
        </header>

        <!-- ===============================================================
             AREA UTAMA: BAGIAN C (SOAL DI KIRI) & BAGIAN B (GRID DI KANAN)
             =============================================================== -->
        <main class="cat-main-layout">

            <!-- BAGIAN C: AREA SOAL & TOMBOL AKSI (KIRI) -->
            <section class="cat-question-area">
                <!-- Baris Judul Bab / Sub-Materi (Contoh: UUD 1945) -->
                <div class="cat-topic-banner">
                    <div class="ctb-left">
                        <span class="ctb-category-badge" id="q-category-badge">TWK</span>
                        <strong class="ctb-topic-title" id="q-topic-title">UUD 1945</strong>
                    </div>
                    <div class="ctb-right">
                        <span class="question-stopwatch" title="Waktu Anda pada soal ini">
                            ⏱️ Waktu di soal ini: <strong id="q-current-spent">0d</strong> (Ideal: <span id="q-ideal-time">54</span>d)
                        </span>
                    </div>
                </div>

                <!-- Kotak Berbingkai Soal No.X & Pilihan A-E -->
                <fieldset class="cat-question-fieldset">
                    <legend class="cat-question-legend" id="q-number-legend">Soal No.1</legend>

                    <!-- Teks & Gambar Soal -->
                    <div class="cat-question-text" id="q-text-content">
                        Memuat butir soal...
                    </div>
                    <div id="q-image-wrap" class="cat-question-image" style="display: none;">
                        <img id="q-image-el" src="" alt="Gambar Soal">
                    </div>

                    <!-- Daftar Pilihan Jawaban Radio A, B, C, D, E -->
                    <div class="cat-options-list" id="q-options-container">
                        <!-- Di-render dinamis oleh JavaScript -->
                    </div>
                </fieldset>

                <!-- Tombol Aksi Bawah Sesuai Gambar: "Simpan dan Lanjutkan" & "Lewatkan soal ini" -->
                <div class="cat-bottom-actions">
                    <button type="button" class="btn-cat-action btn-save-next" id="btn-save-next" onclick="handleSaveAndNext()">
                        Simpan dan Lanjutkan
                    </button>
                    <button type="button" class="btn-cat-action btn-skip-next" id="btn-skip-next" onclick="handleSkipQuestion()">
                        Lewatkan soal ini
                    </button>

                    <label class="cat-doubt-toggle">
                        <input type="checkbox" id="chk-doubtful">
                        <span>Tandai Ragu-ragu</span>
                    </label>
                </div>
            </section>

            <!-- BAGIAN B: PANEL NAVIGASI GRID NOMOR SOAL (KANAN) -->
            <aside class="cat-sidebar-nav">
                <div class="csn-card">
                    <div class="csn-head">
                        <span>Navigasi Nomor Soal</span>
                        <small id="csn-progress-text">0/0 Terjawab</small>
                    </div>

                    <!-- Grid 5 Kolom Kotak Nomor Soal (Hijau = Dijawab + Huruf, Merah = Belum) -->
                    <div class="cat-number-grid" id="cat-number-grid">
                        <!-- Di-render dinamis oleh JavaScript -->
                    </div>

                    <!-- Tombol Panah Navigasi Kiri & Kanan Sesuai Gambar B -->
                    <div class="cat-arrow-controls">
                        <button type="button" class="btn-circle-arrow" onclick="navigateRelative(-1)" title="Soal Sebelumnya">
                            &#8592;
                        </button>
                        <button type="button" class="btn-circle-arrow" onclick="navigateRelative(1)" title="Soal Selanjutnya">
                            &#8594;
                        </button>
                    </div>

                    <!-- Keterangan Warna Legend -->
                    <div class="csn-legend">
                        <div class="leg-item"><span class="leg-box leg-green">1 A</span> Sudah Dijawab</div>
                        <div class="leg-item"><span class="leg-box leg-red">2</span> Belum Dijawab</div>
                        <div class="leg-item"><span class="leg-box leg-amber">3 ?</span> Ragu-ragu</div>
                    </div>
                </div>
            </aside>

        </main>
    </div>

    <!-- =====================================================================
         POP-UP MODAL KONFIRMASI SELESAI UJIAN
         ===================================================================== -->
    <div id="modal-finish-exam" class="cat-modal-overlay">
        <div class="cat-modal-box">
            <div class="cm-icon">🏁</div>
            <h2 class="cm-title">Konfirmasi Selesai Ujian</h2>
            <p class="cm-desc">
                Apakah Anda yakin ingin mengakhiri ujian dan mengirim seluruh jawaban sekarang?
            </p>
            <div class="cm-summary">
                <div><span>Total Soal:</span> <strong id="fin-total">0</strong></div>
                <div><span>Sudah Dijawab:</span> <strong class="txt-green" id="fin-answered">0</strong></div>
                <div><span>Belum Dijawab:</span> <strong class="txt-red" id="fin-unanswered">0</strong></div>
            </div>
            <div class="cm-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeFinishModal()">Kembali ke Soal</button>
                <button type="button" id="btn-confirm-finish" class="btn-modal-confirm" onclick="submitFinishExam()">
                    ✅ Ya, Selesai & Lihat Skor
                </button>
            </div>
        </div>
    </div>

    <!-- Konfigurasi Endpoint untuk JS -->
    <script>
        window.CAT_CONFIG = {
            tryoutId: {{ $tryout->id }},
            startUrl: "{{ route('cat.start', $tryout->id) }}",
            answerUrlTemplate: "{{ url('/dashboard/tryout/attempt') }}/:attemptId/answer",
            finishUrlTemplate: "{{ url('/dashboard/tryout/attempt') }}/:attemptId/finish",
            csrfToken: "{{ csrf_token() }}"
        };
    </script>
    <script src="{{ asset('dashboard/js/cat-exam.js') }}"></script>
</body>
</html>