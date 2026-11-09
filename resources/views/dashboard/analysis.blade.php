@extends('dashboard.layouts.app')

@section('title', 'Analisis Tryout: ' . $attempt->tryout_title)
@section('topbar_title', '📊 Analisis & Pembahasan Tryout')
@section('topbar_subtitle', $attempt->tryout_title)

@section('content')
    {{-- Pemanggilan CSS Terpisah Khusus Halaman Analisis --}}
    <link rel="stylesheet" href="{{ asset('dashboard/css/analysis.css') }}">

    <div class="an-workspace">

        <!-- ===================================================================
             HEADER COMPACT + STICKY TOP NAVBAR (TANPA PERLU SCROLL JAUH)
             =================================================================== -->
        <div class="an-top-shell">
            <!-- Baris Atas: Judul Tryout, Status Lulus, & Pemilih Sesi -->
            <div class="an-compact-header">
                <div class="an-ch-left">
                    <a href="{{ route('dashboard', ['tab' => 'tab-diagnostic']) }}" class="an-btn-back" title="Kembali ke Daftar Hasil">
                        &larr; Daftar Hasil
                    </a>

                    <div class="an-ch-title-group">
                        <div class="an-ch-badges">
                            <span class="an-badge-type">
                                {{ strtoupper(str_replace('_', ' ', $attempt->tryout_type ?? 'SIMULASI CBT')) }}
                            </span>
                            <span class="an-badge-pass {{ $attempt->is_passed ? 'is-pass' : 'is-fail' }}">
                                {{ $attempt->is_passed ? '✅ LULUS PASSING GRADE' : '⚠️ BELUM LULUS PG' }}
                            </span>
                            <span class="an-meta-inline">
                                🗓️ {{ $attempt->finished_at ? \Carbon\Carbon::parse($attempt->finished_at)->format('d M Y, H:i') : '-' }}
                                • ⚡ Aktual {{ $attemptMetrics['total_time_minutes'] }}/{{ $attempt->duration_minutes }} Menit
                            </span>
                        </div>
                        <h2 class="an-ch-title">{{ $attempt->tryout_title }}</h2>
                    </div>
                </div>

                <div class="an-ch-right">
                    @if ($allAttempts->count() > 1)
                        <select id="attempt-select" class="an-select-session"
                            onchange="if(this.value) window.location.href=this.value"
                            aria-label="Pilih Sesi Tryout Lainnya">
                            @foreach ($allAttempts as $item)
                                <option value="{{ route('dashboard.analysis', $item->id) }}"
                                    {{ $item->id == $attempt->id ? 'selected' : '' }}>
                                    {{ $item->tryout_title }} ({{ $item->total_score }} Poin • {{ $item->finished_at ? \Carbon\Carbon::parse($item->finished_at)->format('d/m/y') : 'Selesai' }})
                                </option>
                            @endforeach
                        </select>
                    @endif

                    <a href="{{ route('cat.show', $attempt->tryout_id) }}" class="an-btn-retry">
                        🔄 Coba Lagi
                    </a>
                </div>
            </div>

            <!-- Baris Bawah: NAVBAR MENU TAB ATAS -->
            <nav class="an-top-navbar" id="analysis-top-navbar">
                <button type="button" class="an-nav-tab active" data-an-tab="panel-overview">
                    <span class="tab-ic">📊</span>
                    <span>Ringkasan & Passing Grade</span>
                </button>

                <button type="button" class="an-nav-tab" data-an-tab="panel-questions">
                    <span class="tab-ic">📝</span>
                    <span>Bedah Soal & Pembahasan</span>
                    <span class="tab-pill">{{ $questionLogs->count() }} Soal</span>
                </button>

                <button type="button" class="an-nav-tab" data-an-tab="panel-competency">
                    <span class="tab-ic">🧭</span>
                    <span>Peta Kompetensi Bab</span>
                    <span class="tab-pill">{{ $attemptTopics->count() }} Topik</span>
                </button>

                <button type="button" class="an-nav-tab" data-an-tab="panel-timelog">
                    <span class="tab-ic">⏱️</span>
                    <span>Audit Kecepatan Waktu</span>
                    @if ($attemptMetrics['overtime_count'] > 0)
                        <span class="tab-pill pill-danger">{{ $attemptMetrics['overtime_count'] }} Overtime</span>
                    @endif
                </button>
            </nav>
        </div>

        <!-- ===================================================================
             TAB 1: RINGKASAN SKOR & EVALUASI PASSING GRADE PER KATEGORI
             =================================================================== -->
        <section class="an-tab-panel active" id="panel-overview">
            <!-- 6 Kartu KPI Ringkas -->
            <div class="an-kpi-row">
                <div class="an-kpi-box kpi-blue">
                    <div class="kpi-ic">🏆</div>
                    <div>
                        <span class="kpi-lbl">Skor Akhir Total</span>
                        <h3 class="kpi-num">{{ $attempt->total_score }} <small>Poin</small></h3>
                    </div>
                </div>

                <div class="an-kpi-box kpi-emerald">
                    <div class="kpi-ic">🎯</div>
                    <div>
                        <span class="kpi-lbl">Ketepatan Kunci</span>
                        <h3 class="kpi-num">{{ $attemptMetrics['accuracy'] }}% <small>({{ $attemptMetrics['total_correct'] }}/{{ $attemptMetrics['total_questions'] }})</small></h3>
                    </div>
                </div>

                <div class="an-kpi-box kpi-indigo">
                    <div class="kpi-ic">📋</div>
                    <div>
                        <span class="kpi-lbl">Rincian Status</span>
                        <h3 class="kpi-num">
                            <span class="c-emerald">{{ $attemptMetrics['total_correct'] }}B</span> ·
                            @if ($attemptMetrics['total_partial'] > 0)
                                <span class="c-amber">{{ $attemptMetrics['total_partial'] }}P</span> ·
                            @endif
                            <span class="c-rose">{{ $attemptMetrics['total_wrong'] }}S</span> ·
                            <span class="c-muted">{{ $attemptMetrics['total_empty'] }}K</span>
                        </h3>
                    </div>
                </div>

                <div class="an-kpi-box kpi-amber">
                    <div class="kpi-ic">⚡</div>
                    <div>
                        <span class="kpi-lbl">Rata-rata Waktu</span>
                        <h3 class="kpi-num">{{ $attemptMetrics['avg_speed'] }} <small>dtk/soal</small></h3>
                    </div>
                </div>

                <div class="an-kpi-box kpi-rose">
                    <div class="kpi-ic">⏳</div>
                    <div>
                        <span class="kpi-lbl">Soal Overtime</span>
                        <h3 class="kpi-num">{{ $attemptMetrics['overtime_count'] }} <small>Butir</small></h3>
                    </div>
                </div>

                <div class="an-kpi-box kpi-purple">
                    <div class="kpi-ic">🚩</div>
                    <div>
                        <span class="kpi-lbl">Ditandai Ragu</span>
                        <h3 class="kpi-num">{{ $attemptMetrics['doubtful_count'] }} <small>Butir</small></h3>
                    </div>
                </div>
            </div>

            <!-- Grid Evaluasi Passing Grade + Pintasan Cepat ke Pembahasan -->
            <div class="an-overview-grid">
                <div class="an-card">
                    <div class="an-card-header">
                        <div>
                            <h3 class="an-card-title">🏛️ Evaluasi Skor & Ambang Batas (Passing Grade)</h3>
                            <p class="an-card-sub">Perbandingan skor per sub-tes terhadap standar kelulusan</p>
                        </div>
                        <button type="button" class="an-btn-primary-sm js-switch-tab" data-target-tab="panel-questions">
                            📝 Lihat Pembahasan Soal &rarr;
                        </button>
                    </div>

                    @if (isset($categoryBreakdown) && $categoryBreakdown->count() > 0)
                        <div class="an-pg-grid">
                            @foreach ($categoryBreakdown as $catItem)
                                <div class="an-pg-item">
                                    <div class="an-pg-top">
                                        <div class="an-pg-title">
                                            <span class="an-cat-pill cat-{{ strtolower($catItem->category) }}">
                                                {{ $catItem->category }}
                                            </span>
                                            <div>
                                                <h4>Sub-Tes {{ $catItem->category }}</h4>
                                                <small>{{ $catItem->total_questions }} Soal • Rata-rata {{ $catItem->avg_time }} dtk/soal</small>
                                            </div>
                                        </div>
                                        <div class="an-pg-score">
                                            <strong>{{ $catItem->score_earned }}</strong>
                                            <span>/ PG {{ $catItem->passing_grade }}</span>
                                            <em class="pg-badge {{ $catItem->is_passed ? 'pass' : 'fail' }}">
                                                {{ $catItem->is_passed ? '✓ Lulus PG' : '✕ Belum Lulus' }}
                                            </em>
                                        </div>
                                    </div>

                                    <div class="an-bar-track">
                                        <div class="an-bar-fill {{ $catItem->is_passed ? 'fill-emerald' : 'fill-rose' }}"
                                            style="width: {{ $catItem->percentage }}%"></div>
                                    </div>

                                    <div class="an-pg-foot">
                                        <span>Skor Maksimal: <strong>{{ $catItem->max_score }} Poin</strong></span>
                                        <span>Benar Kunci: <strong>{{ $catItem->correct_count }}/{{ $catItem->total_questions }} Soal</strong></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="an-empty">Belum ada rincian kategori untuk sesi ini.</p>
                    @endif
                </div>

                <!-- Ringkasan Cepat Diagnostik & Pintasan -->
                <div class="an-card">
                    <div class="an-card-header">
                        <div>
                            <h3 class="an-card-title">💡 Ringkasan Diagnostik Cepat</h3>
                            <p class="an-card-sub">Ikhtisar penguasaan topik pada sesi ini</p>
                        </div>
                    </div>

                    <div class="an-quick-diag-boxes">
                        <div class="qd-box qd-emerald">
                            <strong>{{ $attemptMetrics['unggul_count'] }}</strong>
                            <span>Topik Unggul (≥75%)</span>
                        </div>
                        <div class="qd-box qd-amber">
                            <strong>{{ $attemptMetrics['cukup_count'] }}</strong>
                            <span>Topik Cukup (50–74%)</span>
                        </div>
                        <div class="qd-box qd-rose">
                            <strong>{{ $attemptMetrics['lemah_count'] }}</strong>
                            <span>Topik Lemah (&lt;50%)</span>
                        </div>
                    </div>

                    <div class="an-shortcut-stack">
                        <button type="button" class="an-shortcut-btn js-open-filtered-questions" data-filter="wrong">
                            <span>🔍 Pelajari <strong>{{ $attemptMetrics['total_wrong'] }} Soal yang Dijawab Salah</strong></span>
                            <span>&rarr;</span>
                        </button>
                        <button type="button" class="an-shortcut-btn js-open-filtered-questions" data-filter="overtime">
                            <span>⏳ Evaluasi <strong>{{ $attemptMetrics['overtime_count'] }} Soal yang Overtime (Lama)</strong></span>
                            <span>&rarr;</span>
                        </button>
                        <button type="button" class="an-shortcut-btn js-switch-tab" data-target-tab="panel-competency">
                            <span>🧭 Buka <strong>Rapor Peta Kompetensi Bab & Video Materi</strong></span>
                            <span>&rarr;</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===================================================================
             TAB 2: BEDAH SOAL & PEMBAHASAN PER BUTIR SOAL (SPLIT VIEW TANPA SCROLL JAUH)
             =================================================================== -->
        <section class="an-tab-panel" id="panel-questions">
            @if ($questionLogs->count() > 0)
                <div class="an-q-split-layout">

                    <!-- KOLOM KIRI: TAMPILAN PER SOAL + OPSI + PEMBAHASAN -->
                    <div class="an-q-main-stage">

                        <!-- Bar Navigasi Atas Soal (Prev / Indikator / Next) -->
                        <div class="an-q-stage-nav">
                            <button type="button" class="an-nav-arrow-btn" id="btn-prev-q">
                                &larr; Soal Sebelumnya
                            </button>

                            <div class="an-stage-counter">
                                <span id="active-q-position">Soal 1 dari {{ $questionLogs->count() }}</span>
                                <small class="kb-hint">(Gunakan tombol ◀ ▶ di keyboard untuk berpindah soal)</small>
                            </div>

                            <button type="button" class="an-nav-arrow-btn primary" id="btn-next-q">
                                Soal Selanjutnya &rarr;
                            </button>
                        </div>

                        <!-- Koleksi Kartu Soal (Ditampilkan 1 Per 1 Secara Instan) -->
                        <div id="question-stage-deck">
                            @foreach ($questionLogs as $idx => $qLog)
                                @php
                                    $timeDiff = $qLog->time_spent_seconds - $qLog->ideal_time_seconds;
                                @endphp

                                <article class="an-q-slide {{ $idx === 0 ? 'is-active' : '' }}"
                                    id="q-slide-{{ $qLog->display_number }}"
                                    data-q-index="{{ $idx }}"
                                    data-q-num="{{ $qLog->display_number }}"
                                    data-status="{{ $qLog->answer_status }}"
                                    data-overtime="{{ $qLog->is_overtime ? '1' : '0' }}"
                                    data-doubtful="{{ $qLog->is_doubtful ? '1' : '0' }}"
                                    data-search="{{ strtolower($qLog->question_text . ' ' . $qLog->chapter . ' ' . $qLog->sub_chapter . ' ' . $qLog->category) }}">

                                    <!-- 1. Header Informasi Soal -->
                                    <div class="an-qs-header">
                                        <div class="an-qs-badges">
                                            <span class="qs-num">SOAL #{{ $qLog->display_number }}</span>
                                            <span class="an-cat-pill cat-{{ strtolower($qLog->category) }}">{{ $qLog->category }}</span>
                                            <span class="qs-topic-path">
                                                <strong>{{ $qLog->chapter }}</strong> &rsaquo; {{ $qLog->sub_chapter }}
                                            </span>
                                        </div>

                                        <div class="an-qs-meta">
                                            @if ($qLog->is_doubtful)
                                                <span class="qs-tag tag-doubt">🚩 Ditandai Ragu</span>
                                            @endif

                                            <span class="qs-tag {{ $qLog->is_overtime ? 'tag-overtime' : 'tag-ideal' }}">
                                                ⏱️ {{ $qLog->time_spent_seconds }} dtk
                                                <small>(Ideal {{ $qLog->ideal_time_seconds }}s • {{ $qLog->visit_count }}x kunjungan)</small>
                                            </span>

                                            <span class="qs-result-badge res-{{ $qLog->answer_status }}">
                                                @if ($qLog->answer_status === 'correct')
                                                    ✓ BENAR (+{{ $qLog->score_earned }} Poin)
                                                @elseif ($qLog->answer_status === 'partial')
                                                    ⚡ POIN PARSIAL (+{{ $qLog->score_earned }}/{{ $qLog->max_weight }})
                                                @elseif ($qLog->answer_status === 'empty')
                                                    ⚪ KOSONG (0 Poin)
                                                @else
                                                    ✕ SALAH ({{ $qLog->score_earned }} Poin)
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    <!-- 2. Kotak Naskah Pertanyaan -->
                                    <div class="an-qs-question-box">
                                        <div class="an-qs-text">
                                            {!! nl2br(e($qLog->question_text)) !!}
                                        </div>

                                        @if (!empty($qLog->question_image))
                                            <div class="an-qs-img-wrap">
                                                <img src="{{ $qLog->question_image }}"
                                                    alt="Gambar Soal #{{ $qLog->display_number }}"
                                                    class="an-zoomable-img"
                                                    data-img-title="Gambar Soal #{{ $qLog->display_number }}">
                                                <span class="img-zoom-hint">🔍 Klik gambar untuk memperbesar</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- 3. Banner Perbandingan: Pilihan Anda vs Kunci Jawaban Terbaik -->
                                    <div class="an-compare-banner">
                                        <div class="cmp-card {{ $qLog->answer_status === 'correct' ? 'cmp-ok' : ($qLog->answer_status === 'partial' ? 'cmp-warn' : 'cmp-err') }}">
                                            <div class="cmp-label">🙋‍♂️ Opsi yang Anda Pilih</div>
                                            @if ($qLog->selected_option)
                                                <div class="cmp-main">
                                                    <span class="cmp-opt-badge">{{ $qLog->selected_option->option_label }}</span>
                                                    <div class="cmp-opt-info">
                                                        <strong>{{ $qLog->selected_option->option_text }}</strong>
                                                        <small>Perolehan Skor: +{{ $qLog->selected_option->score_weight }} Poin</small>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="cmp-main">
                                                    <span class="cmp-opt-badge">-</span>
                                                    <div class="cmp-opt-info">
                                                        <strong>Tidak Dijawab (Kosong)</strong>
                                                        <small>Perolehan Skor: 0 Poin</small>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="cmp-card cmp-key">
                                            <div class="cmp-label">🔑 Kunci Jawaban Terbaik</div>
                                            @if ($qLog->correct_option)
                                                <div class="cmp-main">
                                                    <span class="cmp-opt-badge">{{ $qLog->correct_option->option_label }}</span>
                                                    <div class="cmp-opt-info">
                                                        <strong>{{ $qLog->correct_option->option_text }}</strong>
                                                        <small>Bobot Maksimal: +{{ $qLog->correct_option->score_weight }} Poin</small>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="cmp-main">
                                                    <div class="cmp-opt-info"><strong>Kunci belum tersedia</strong></div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- 4. Daftar Lengkap Opsi A s.d. E -->
                                    <div class="an-qs-options-block">
                                        <div class="an-block-label">📋 Rincian Opsi Jawaban & Bobot Poin:</div>
                                        <div class="an-opt-stack">
                                            @foreach ($qLog->options as $opt)
                                                @php
                                                    $isPicked = $qLog->selected_option_id == $opt->id;
                                                    $isKey = $qLog->correct_option && $qLog->correct_option->id == $opt->id;
                                                    $isPartial = $opt->score_weight > 0 && !$isKey;

                                                    $rowTone = 'tone-default';
                                                    if ($isKey && $isPicked) {
                                                        $rowTone = 'tone-correct-picked';
                                                    } elseif ($isKey) {
                                                        $rowTone = 'tone-key-only';
                                                    } elseif ($isPicked && $isPartial) {
                                                        $rowTone = 'tone-partial-picked';
                                                    } elseif ($isPicked) {
                                                        $rowTone = 'tone-wrong-picked';
                                                    }
                                                @endphp

                                                <div class="an-opt-item {{ $rowTone }}">
                                                    <div class="opt-circle">{{ $opt->option_label }}</div>

                                                    <div class="opt-body">
                                                        <span class="opt-txt">{{ $opt->option_text }}</span>
                                                        @if (!empty($opt->option_image))
                                                            <div class="opt-img-holder">
                                                                <img src="{{ $opt->option_image }}"
                                                                    alt="Opsi {{ $opt->option_label }}"
                                                                    class="an-zoomable-img"
                                                                    data-img-title="Soal #{{ $qLog->display_number }} - Opsi {{ $opt->option_label }}">
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="opt-tags">
                                                        <span class="opt-point-pill {{ $opt->score_weight >= $qLog->max_weight ? 'pt-max' : ($opt->score_weight > 0 ? 'pt-mid' : 'pt-zero') }}">
                                                            +{{ $opt->score_weight }} Poin
                                                        </span>

                                                        @if ($isPicked)
                                                            <span class="opt-user-badge {{ $isKey ? 'ub-right' : ($isPartial ? 'ub-partial' : 'ub-wrong') }}">
                                                                {{ $isKey ? '✓ Pilihan Anda' : 'Pilihan Anda' }}
                                                            </span>
                                                        @endif

                                                        @if ($isKey)
                                                            <span class="opt-key-badge">🔑 Kunci</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- 5. Panel Pembahasan Lengkap Soal Ini -->
                                    <div class="an-qs-discussion-card">
                                        <div class="an-qsd-top">
                                            <div class="qsd-title">
                                                <span>💡</span>
                                                <div>
                                                    <h4>Pembahasan & Konsep Kunci — Soal #{{ $qLog->display_number }}</h4>
                                                    <small>Sub-Bab: {{ $qLog->sub_chapter }} • Waktu Ideal: {{ $qLog->ideal_time_seconds }} detik</small>
                                                </div>
                                            </div>

                                            <div class="qsd-btns">
                                                @if (!empty($qLog->discussion_video_url))
                                                    <button type="button" class="an-btn-vid trigger-disc-video"
                                                        data-video-url="{{ $qLog->discussion_video_url }}"
                                                        data-video-title="Pembahasan Soal #{{ $qLog->display_number }} ({{ $qLog->sub_chapter }})">
                                                        ▶ Video Pembahasan
                                                    </button>
                                                @endif

                                                <button type="button" class="an-btn-copy"
                                                    data-copy-target="disc-content-{{ $qLog->display_number }}">
                                                    📋 Salin Pembahasan
                                                </button>
                                            </div>
                                        </div>

                                        <div class="an-qsd-content" id="disc-content-{{ $qLog->display_number }}">
                                            @if (!empty($qLog->discussion_text))
                                                {!! nl2br(e($qLog->discussion_text)) !!}
                                            @else
                                                <p>
                                                    Berdasarkan konsep <strong>{{ $qLog->chapter }} ({{ $qLog->sub_chapter }})</strong>,
                                                    jawaban yang paling tepat dengan bobot poin tertinggi (+{{ $qLog->max_weight }} Poin) adalah
                                                    <strong>Opsi {{ $qLog->correct_option->option_label ?? '-' }}</strong>:
                                                    <em>"{{ $qLog->correct_option->option_text ?? '-' }}"</em>.
                                                </p>
                                            @endif
                                        </div>

                                        @if (!empty($qLog->discussion_image))
                                            <div class="an-qsd-img-box">
                                                <span>🖼️ Ilustrasi / Rumus Pembahasan:</span>
                                                <img src="{{ $qLog->discussion_image }}"
                                                    alt="Ilustrasi Pembahasan #{{ $qLog->display_number }}"
                                                    class="an-zoomable-img"
                                                    data-img-title="Ilustrasi Pembahasan Soal #{{ $qLog->display_number }}">
                                            </div>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <!-- State Kosong Jika Filter Tidak Menemukan Soal -->
                        <div class="an-empty-box" id="q-filter-empty" style="display: none;">
                            <h4>🔍 Tidak ada soal yang sesuai dengan filter ini</h4>
                            <p>Silakan pilih filter <strong>Semua</strong> atau hapus kata kunci pencarian.</p>
                            <button type="button" class="an-btn-retry" id="btn-reset-q-filter">Reset Filter</button>
                        </div>
                    </div>

                    <!-- KOLOM KANAN: SIDEBAR NAVIGASI NOMOR SOAL & FILTER (STICKY) -->
                    <aside class="an-q-sidebar">
                        <div class="an-qs-side-card">
                            <h4 class="side-heading">🧭 Navigasi Cepat Nomor Soal</h4>

                            <!-- Input Cari Soal -->
                            <input type="text" id="q-search-input" class="an-side-search"
                                placeholder="🔍 Cari kata kunci soal / bab...">

                            <!-- Filter Pills Status Soal -->
                            <div class="an-side-filters" id="q-status-filters">
                                <button type="button" class="sf-pill active" data-q-filter="all">
                                    Semua ({{ $questionLogs->count() }})
                                </button>
                                <button type="button" class="sf-pill sf-ok" data-q-filter="correct">
                                    ✓ Benar ({{ $attemptMetrics['total_correct'] }})
                                </button>
                                @if ($attemptMetrics['total_partial'] > 0)
                                    <button type="button" class="sf-pill sf-warn" data-q-filter="partial">
                                        ⚡ Parsial ({{ $attemptMetrics['total_partial'] }})
                                    </button>
                                @endif
                                <button type="button" class="sf-pill sf-err" data-q-filter="wrong">
                                    ✕ Salah ({{ $attemptMetrics['total_wrong'] }})
                                </button>
                                @if ($attemptMetrics['total_empty'] > 0)
                                    <button type="button" class="sf-pill" data-q-filter="empty">
                                        ⚪ Kosong ({{ $attemptMetrics['total_empty'] }})
                                    </button>
                                @endif
                                <button type="button" class="sf-pill sf-ot" data-q-filter="overtime">
                                    ⏳ Overtime ({{ $attemptMetrics['overtime_count'] }})
                                </button>
                                @if ($attemptMetrics['doubtful_count'] > 0)
                                    <button type="button" class="sf-pill sf-doubt" data-q-filter="doubtful">
                                        🚩 Ragu ({{ $attemptMetrics['doubtful_count'] }})
                                    </button>
                                @endif
                            </div>

                            <!-- Grid Nomor Soal 1..N -->
                            <div class="an-num-matrix" id="q-number-matrix">
                                @foreach ($questionLogs as $idx => $qLog)
                                    <button type="button"
                                        class="matrix-btn m-{{ $qLog->answer_status }} {{ $idx === 0 ? 'is-current' : '' }}"
                                        data-goto-index="{{ $idx }}"
                                        title="Soal #{{ $qLog->display_number }} ({{ $qLog->category }} - {{ $qLog->sub_chapter }})">
                                        {{ $qLog->display_number }}
                                        @if ($qLog->is_doubtful)
                                            <i class="m-badge">🚩</i>
                                        @elseif ($qLog->is_overtime)
                                            <i class="m-badge">⚡</i>
                                        @endif
                                    </button>
                                @endforeach
                            </div>

                            <!-- Legenda Warna Nomor -->
                            <div class="an-matrix-legend">
                                <span><b class="lg-dot lg-ok"></b> Benar</span>
                                @if ($attemptMetrics['total_partial'] > 0)
                                    <span><b class="lg-dot lg-warn"></b> Poin 1–4</span>
                                @endif
                                <span><b class="lg-dot lg-err"></b> Salah</span>
                                <span><b class="lg-dot lg-empty"></b> Kosong</span>
                                <span>⚡ Overtime</span>
                            </div>
                        </div>
                    </aside>

                </div>
            @else
                <div class="an-empty-box">
                    <p>Belum ada data butir soal pada sesi tryout ini.</p>
                </div>
            @endif
        </section>

        <!-- ===================================================================
             TAB 3: PETA KOMPETENSI BAB & REKOMENDASI VIDEO MATERI
             =================================================================== -->
        <section class="an-tab-panel" id="panel-competency">
            <div class="an-card">
                <div class="an-card-header">
                    <div>
                        <h3 class="an-card-title">🧭 Rapor Competency Diagnostic Per Sub-Bab</h3>
                        <p class="an-card-sub">
                            Unggul: <strong class="c-emerald">{{ $attemptMetrics['unggul_count'] }} Topik</strong> •
                            Cukup: <strong class="c-amber">{{ $attemptMetrics['cukup_count'] }} Topik</strong> •
                            Lemah: <strong class="c-rose">{{ $attemptMetrics['lemah_count'] }} Topik</strong>
                        </p>
                    </div>
                </div>

                @if ($attemptTopics->count() > 0)
                    <div class="an-comp-grid">
                        @foreach ($attemptTopics as $diag)
                            @php $stClass = strtolower($diag->status_label); @endphp
                            <div class="an-comp-card tone-{{ $stClass }}">
                                <div class="comp-top">
                                    <span class="an-cat-pill cat-{{ strtolower($diag->category ?? 'tiu') }}">
                                        {{ $diag->category ?? $diag->subject_name }}
                                    </span>
                                    <span class="comp-status badge-{{ $stClass }}">{{ $diag->status_label }}</span>
                                </div>

                                <h4 class="comp-title">{{ $diag->topic_name }}</h4>
                                <p class="comp-chapter">{{ $diag->chapter ?? $diag->subject_name }}</p>

                                <div class="comp-metrics">
                                    <div>
                                        <small>Akurasi Menjawab</small>
                                        <strong>{{ number_format($diag->accuracy_percentage, 0) }}% ({{ $diag->total_correct }}/{{ $diag->total_answered }})</strong>
                                    </div>
                                    <div>
                                        <small>Rata-rata Kecepatan</small>
                                        <strong>{{ number_format($diag->avg_time_seconds, 0) }} dtk/soal</strong>
                                    </div>
                                </div>

                                <div class="an-bar-track">
                                    <div class="an-bar-fill fill-{{ $stClass }}"
                                        style="width: {{ $diag->accuracy_percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="an-empty">Belum ada rincian pemetaan topik pada sesi ini.</p>
                @endif
            </div>

            @if ($recommendedMaterials->count() > 0)
                <div class="an-card mt-16">
                    <div class="an-card-header">
                        <div>
                            <h3 class="an-card-title">🎬 Rekomendasi Video Penguatan Konsep</h3>
                            <p class="an-card-sub">Dipilih otomatis untuk menambal sub-bab Anda yang masih Lemah/Cukup</p>
                        </div>
                        <a href="{{ route('dashboard', ['tab' => 'tab-materials']) }}" class="an-btn-retry">
                            Semua Video &rarr;
                        </a>
                    </div>

                    <div class="an-video-grid">
                        @foreach ($recommendedMaterials as $mat)
                            <div class="an-video-card">
                                <div>
                                    <span class="an-cat-pill cat-tiu">{{ $mat->subject_name }} • {{ $mat->topic_name }}</span>
                                    <h4>{{ $mat->title }}</h4>
                                    <p>👨‍🏫 {{ $mat->instructor_name ?? 'Mentor Forecasting Academy' }}</p>
                                </div>
                                <button type="button" class="an-btn-primary-sm w-full trigger-disc-video"
                                    data-video-url="{{ $mat->content_url ?? '' }}"
                                    data-video-title="{{ $mat->title }}">
                                    ▶ Tonton Video Materi
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>

        <!-- ===================================================================
             TAB 4: AUDIT KECEPATAN WAKTU & KUNJUNGAN PER SOAL
             =================================================================== -->
        <section class="an-tab-panel" id="panel-timelog">
            <div class="an-card">
                <div class="an-card-header">
                    <div>
                        <h3 class="an-card-title">⏱️ Tabel Audit Kecepatan & Kunjungan Per Butir Soal</h3>
                        <p class="an-card-sub">Klik tombol <strong>🔍 Bedah Soal</strong> untuk langsung membuka soal & pembahasannya</p>
                    </div>

                    <div class="an-side-filters">
                        <button type="button" class="sf-pill active" data-filter-log="all">
                            Semua ({{ $questionLogs->count() }})
                        </button>
                        <button type="button" class="sf-pill sf-err" data-filter-log="overtime">
                            Overtime ({{ $attemptMetrics['overtime_count'] }})
                        </button>
                        <button type="button" class="sf-pill sf-ok" data-filter-log="ideal">
                            Ideal & Cepat ({{ $questionLogs->count() - $attemptMetrics['overtime_count'] }})
                        </button>
                    </div>
                </div>

                @if ($questionLogs->count() > 0)
                    <div class="an-table-container">
                        <table class="an-audit-table" id="question-log-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kategori & Sub-Bab</th>
                                    <th>Cuplikan Soal</th>
                                    <th>Pilihan Anda</th>
                                    <th>Kunci</th>
                                    <th>Waktu Anda</th>
                                    <th>Ideal</th>
                                    <th>Kunjungan</th>
                                    <th>Evaluasi Waktu</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($questionLogs as $idx => $log)
                                    <tr data-log-type="{{ $log->is_overtime ? 'overtime' : 'ideal' }}">
                                        <td><strong>#{{ $log->display_number }}</strong></td>
                                        <td>
                                            <span class="an-cat-pill cat-{{ strtolower($log->category) }}">{{ $log->category }}</span>
                                            <div class="td-sub">{{ $log->sub_chapter }}</div>
                                        </td>
                                        <td>{{ \Illuminate\Support\Str::limit($log->question_text, 55) }}</td>
                                        <td>
                                            @if ($log->selected_option)
                                                <strong>{{ $log->selected_option->option_label }}</strong>
                                                <small>({{ $log->score_earned }}p)</small>
                                            @else
                                                <span class="c-muted">Kosong</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong class="c-emerald">{{ $log->correct_option->option_label ?? '-' }}</strong>
                                            <small>(+{{ $log->max_weight }}p)</small>
                                        </td>
                                        <td><strong>{{ $log->time_spent_seconds }} dtk</strong></td>
                                        <td>{{ $log->ideal_time_seconds }} dtk</td>
                                        <td>{{ $log->visit_count }}x {!! $log->is_doubtful ? '🚩' : '' !!}</td>
                                        <td>
                                            <span class="pg-badge {{ $log->is_overtime ? 'fail' : 'pass' }}">
                                                {{ $log->is_overtime ? 'Overtime (+' . ($log->time_spent_seconds - $log->ideal_time_seconds) . 's)' : 'Ideal' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="pg-badge {{ $log->is_answer_correct ? 'pass' : ($log->is_partial ? 'warn' : 'fail') }}">
                                                {{ $log->is_answer_correct ? '✓ Benar' : ($log->is_partial ? '⚡ Poin ' . $log->score_earned : '✕ Salah') }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="an-btn-inspect js-inspect-question"
                                                data-inspect-index="{{ $idx }}">
                                                🔍 Bedah Soal
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>

    </div>

    <!-- MODAL ZOOM GAMBAR -->
    <div class="an-modal" id="an-image-modal">
        <div class="an-modal-box">
            <div class="an-modal-head">
                <h4 id="an-img-modal-title">Pratinjau Gambar</h4>
                <button type="button" class="an-modal-close" id="an-img-modal-close">&times;</button>
            </div>
            <div class="an-modal-body center">
                <img src="" alt="Preview" id="an-img-modal-target" class="an-modal-img">
            </div>
        </div>
    </div>

    <!-- MODAL VIDEO PEMBAHASAN -->
    <div class="an-modal" id="an-video-modal">
        <div class="an-modal-box modal-lg">
            <div class="an-modal-head">
                <h4 id="an-vid-modal-title">🎬 Video Pembahasan</h4>
                <button type="button" class="an-modal-close" id="an-vid-modal-close">&times;</button>
            </div>
            <div class="an-modal-body">
                <div class="an-video-embed" id="an-video-container"></div>
            </div>
        </div>
    </div>

    {{-- Pemanggilan JS Terpisah Khusus Halaman Analisis --}}
    <script src="{{ asset('dashboard/js/analysis.js') }}"></script>
@endsection