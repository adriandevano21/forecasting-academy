/**
 * MESIN UJIAN CAT (COMPUTER ASSISTED TEST) - FORECASTING ACADEMY
 * Lokasi: public/dashboard/js/cat-exam.js
 */

let catState = {
    attemptId: null,
    questions: [],
    currentIndex: 0,
    remainingSeconds: 0,
    countdownInterval: null,
    questionTimerInterval: null,
    questionOpenedAt: null,
    currentQuestionLiveSeconds: 0
};

/* ==========================================================================
   1. MODAL KONFIRMASI MULAI UJIAN
   ========================================================================== */
function openConfirmStartModal() {
    const modal = document.getElementById('modal-confirm-start');
    const chk = document.getElementById('chk-ready');
    const btn = document.getElementById('btn-confirm-go');
    if (chk) chk.checked = false;
    if (btn) btn.disabled = true;
    if (modal) modal.classList.add('open');
}

function closeConfirmStartModal() {
    const modal = document.getElementById('modal-confirm-start');
    if (modal) modal.classList.remove('open');
}

function toggleStartButton() {
    const chk = document.getElementById('chk-ready');
    const btn = document.getElementById('btn-confirm-go');
    if (btn && chk) {
        btn.disabled = !chk.checked;
    }
}

/* ==========================================================================
   2. INISIALISASI & MULAI UJIAN CAT (AJAX START)
   ========================================================================== */
async function launchCatEngine() {
    const btn = document.getElementById('btn-confirm-go');
    if (btn) {
        btn.disabled = true;
        btn.textContent = '⏳ Menyiapkan Soal CAT...';
    }

    try {
        const res = await fetch(window.CAT_CONFIG.startUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.CAT_CONFIG.csrfToken
            }
        });

        const data = await res.json();

        if (data.status === 'success') {
            catState.attemptId = data.attempt_id;
            catState.questions = data.questions || [];
            catState.remainingSeconds = data.remaining_seconds || (data.duration_minutes * 60);
            catState.currentIndex = 0;

            closeConfirmStartModal();
            document.getElementById('screen-briefing').style.display = 'none';
            document.getElementById('screen-cat-exam').style.display = 'flex';

            renderHeaderStats();
            renderQuestionGrid();
            loadQuestionByIndex(0);
            startMainCountdown();
        } else {
            alert('Gagal memulai sesi ujian. Silakan muat ulang halaman.');
        }
    } catch (err) {
        console.error(err);
        alert('Terjadi kesalahan koneksi saat memuat soal ujian.');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.textContent = '🔥 Ya, Mulai Ujian Sekarang!';
        }
    }
}

/* ==========================================================================
   3. RENDER HEADER STATISTIK (BAGIAN A) & GRID NAVIGASI 5 KOLOM (BAGIAN B)
   ========================================================================== */
function renderHeaderStats() {
    const total = catState.questions.length;
    const answered = catState.questions.filter(q => q.selected_option_id !== null).length;
    const unanswered = total - answered;

    document.getElementById('hdr-total-q').textContent = total;
    document.getElementById('hdr-answered-q').textContent = answered;
    document.getElementById('hdr-unanswered-q').textContent = unanswered;

    const progText = document.getElementById('csn-progress-text');
    if (progText) {
        progText.textContent = `${answered}/${total} Terjawab`;
    }
}

function renderQuestionGrid() {
    const grid = document.getElementById('cat-number-grid');
    if (!grid) return;

    grid.innerHTML = '';

    catState.questions.forEach((q, idx) => {
        const tile = document.createElement('button');
        tile.type = 'button';
        tile.className = 'cat-num-tile';

        if (q.is_doubtful) {
            tile.classList.add('doubtful');
        } else if (q.selected_option_id !== null) {
            tile.classList.add('answered');
        } else {
            tile.classList.add('unanswered');
        }

        if (idx === catState.currentIndex) {
            tile.classList.add('active-current');
        }

        const badgeText = q.selected_label ? q.selected_label : (q.is_doubtful ? '?' : '');

        tile.innerHTML = `
            <span class="tile-num">${q.number}</span>
            ${badgeText ? `<span class="tile-ans-badge">${badgeText}</span>` : ''}
        `;

        tile.addEventListener('click', () => {
            if (idx !== catState.currentIndex) {
                // Catat waktu di soal saat ini sebelum pindah ke nomor yang diklik
                syncCurrentQuestionTimeOnly().then(() => {
                    loadQuestionByIndex(idx);
                });
            }
        });

        grid.appendChild(tile);
    });
}

/* ==========================================================================
   4. TAMPILKAN SOAL & PILIHAN OPSI A-E (BAGIAN C)
   ========================================================================== */
function loadQuestionByIndex(index) {
    if (index < 0 || index >= catState.questions.length) return;

    catState.currentIndex = index;
    const q = catState.questions[index];

    // Update Banner Kategori & Sub-Bab (Contoh: TWK | UUD 1945)
    document.getElementById('q-category-badge').textContent = q.category || 'CAT';
    document.getElementById('q-topic-title').textContent = `${q.chapter} — ${q.sub_chapter}`;
    document.getElementById('q-ideal-time').textContent = q.ideal_time_seconds || 54;

    // Update Nomor Soal di Legend
    document.getElementById('q-number-legend').textContent = `Soal No.${q.number}`;

    // Update Teks Soal
    document.getElementById('q-text-content').textContent = q.question_text;

    // Update Gambar Soal (Jika Ada)
    const imgWrap = document.getElementById('q-image-wrap');
    const imgEl = document.getElementById('q-image-el');
    if (q.question_image) {
        imgEl.src = q.question_image;
        imgWrap.style.display = 'block';
    } else {
        imgWrap.style.display = 'none';
    }

    // Update Checkbox Ragu-ragu
    const chkDoubt = document.getElementById('chk-doubtful');
    if (chkDoubt) chkDoubt.checked = !!q.is_doubtful;

    // Render Pilihan Jawaban A, B, C, D, E
    const optsContainer = document.getElementById('q-options-container');
    optsContainer.innerHTML = '';

    q.options.forEach(opt => {
        const row = document.createElement('label');
        const isSelected = Number(q.selected_option_id) === Number(opt.id);
        row.className = `cat-option-row ${isSelected ? 'selected' : ''}`;

        row.innerHTML = `
            <input type="radio" name="cat_option_radio" value="${opt.id}" data-label="${opt.label}" ${isSelected ? 'checked' : ''}>
            <span class="opt-label-char">${opt.label}.</span>
            <div class="opt-content-body">
                <span>${opt.text || ''}</span>
                ${opt.image ? `<img src="${opt.image}" alt="Opsi ${opt.label}">` : ''}
            </div>
        `;

        const radio = row.querySelector('input[type="radio"]');
        radio.addEventListener('change', () => {
            document.querySelectorAll('.cat-option-row').forEach(el => el.classList.remove('selected'));
            row.classList.add('selected');
        });

        optsContainer.appendChild(row);
    });

    // Mulai penghitung detik untuk soal yang sedang dibuka
    startPerQuestionStopwatch(q.time_spent_seconds || 0);
    renderQuestionGrid();
}

/* ==========================================================================
   5. STOPWATCH PER BUTIR SOAL & TIMER HITUNG MUNDUR UTAMA
   ========================================================================== */
function startPerQuestionStopwatch(initialSeconds) {
    if (catState.questionTimerInterval) {
        clearInterval(catState.questionTimerInterval);
    }

    catState.questionOpenedAt = Date.now();
    catState.currentQuestionLiveSeconds = initialSeconds;

    const spentEl = document.getElementById('q-current-spent');
    if (spentEl) spentEl.textContent = `${catState.currentQuestionLiveSeconds}d`;

    catState.questionTimerInterval = setInterval(() => {
        const delta = Math.round((Date.now() - catState.questionOpenedAt) / 1000);
        const totalNow = initialSeconds + delta;
        if (spentEl) spentEl.textContent = `${totalNow}d`;
    }, 1000);
}

function getDeltaSecondsOnCurrentQuestion() {
    if (!catState.questionOpenedAt) return 0;
    const delta = Math.max(1, Math.round((Date.now() - catState.questionOpenedAt) / 1000));
    catState.questionOpenedAt = Date.now();
    return delta;
}

function startMainCountdown() {
    if (catState.countdownInterval) {
        clearInterval(catState.countdownInterval);
    }

    const timerBox = document.getElementById('cat-countdown-box');

    const updateTimerDisplay = () => {
        const secs = Math.max(0, catState.remainingSeconds);
        const hrs = String(Math.floor(secs / 3600)).padStart(2, '0');
        const mins = String(Math.floor((secs % 3600) / 60)).padStart(2, '0');
        const s = String(secs % 60).padStart(2, '0');

        if (timerBox) {
            timerBox.textContent = `${hrs}:${mins}:${s}`;
            if (secs <= 300) {
                timerBox.classList.add('warning');
            }
        }
    };

    updateTimerDisplay();

    catState.countdownInterval = setInterval(() => {
        catState.remainingSeconds--;
        updateTimerDisplay();

        if (catState.remainingSeconds <= 0) {
            clearInterval(catState.countdownInterval);
            alert('⏰ Waktu ujian telah habis! Jawaban Anda akan disimpan secara otomatis.');
            submitFinishExam();
        }
    }, 1000);
}

/* ==========================================================================
   6. AKSI TOMBOL "SIMPAN DAN LANJUTKAN" & "LEWATKAN SOAL INI"
   ========================================================================== */
async function handleSaveAndNext() {
    const q = catState.questions[catState.currentIndex];
    const checkedRadio = document.querySelector('input[name="cat_option_radio"]:checked');
    const chkDoubt = document.getElementById('chk-doubtful');

    if (!checkedRadio) {
        alert('Silakan pilih salah satu opsi jawaban (A, B, C, D, atau E) terlebih dahulu, atau klik "Lewatkan soal ini".');
        return;
    }

    const selectedOptId = Number(checkedRadio.value);
    const selectedLabel = checkedRadio.getAttribute('data-label');
    const isDoubtful = chkDoubt ? chkDoubt.checked : false;
    const deltaSeconds = getDeltaSecondsOnCurrentQuestion();

    // Update state lokal terlebih dahulu agar responsif
    q.selected_option_id = selectedOptId;
    q.selected_label = selectedLabel;
    q.is_doubtful = isDoubtful;
    q.time_spent_seconds = (q.time_spent_seconds || 0) + deltaSeconds;

    renderHeaderStats();
    renderQuestionGrid();

    // Kirim ke server di background
    sendAnswerPayload(q.question_id, selectedOptId, deltaSeconds, isDoubtful, 'save');

    // Pindah ke soal berikutnya
    moveToNextQuestion();
}

async function handleSkipQuestion() {
    const q = catState.questions[catState.currentIndex];
    const chkDoubt = document.getElementById('chk-doubtful');
    const isDoubtful = chkDoubt ? chkDoubt.checked : false;
    const deltaSeconds = getDeltaSecondsOnCurrentQuestion();

    q.is_doubtful = isDoubtful;
    q.time_spent_seconds = (q.time_spent_seconds || 0) + deltaSeconds;

    renderHeaderStats();
    renderQuestionGrid();

    sendAnswerPayload(q.question_id, q.selected_option_id, deltaSeconds, isDoubtful, 'skip');

    moveToNextQuestion();
}

async function syncCurrentQuestionTimeOnly() {
    const q = catState.questions[catState.currentIndex];
    if (!q) return;
    const deltaSeconds = getDeltaSecondsOnCurrentQuestion();
    q.time_spent_seconds = (q.time_spent_seconds || 0) + deltaSeconds;
    await sendAnswerPayload(q.question_id, q.selected_option_id, deltaSeconds, q.is_doubtful, 'skip');
}

async function sendAnswerPayload(questionId, optionId, deltaSeconds, isDoubtful, actionType) {
    const url = window.CAT_CONFIG.answerUrlTemplate.replace(':attemptId', catState.attemptId);
    try {
        await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.CAT_CONFIG.csrfToken
            },
            body: JSON.stringify({
                question_id: questionId,
                option_id: optionId,
                delta_seconds: deltaSeconds,
                is_doubtful: isDoubtful,
                action_type: actionType
            })
        });
    } catch (e) {
        console.error('Gagal menyimpan jawaban:', e);
    }
}

function moveToNextQuestion() {
    if (catState.currentIndex < catState.questions.length - 1) {
        loadQuestionByIndex(catState.currentIndex + 1);
    } else {
        loadQuestionByIndex(0); // Kembali ke soal nomor 1 jika sudah di nomor terakhir
    }
}

function navigateRelative(step) {
    const target = catState.currentIndex + step;
    if (target >= 0 && target < catState.questions.length) {
        syncCurrentQuestionTimeOnly().then(() => {
            loadQuestionByIndex(target);
        });
    }
}

/* ==========================================================================
   7. SELESAI UJIAN & REDIRECT KE HASIL DIAGNOSTIC
   ========================================================================== */
function openFinishModal() {
    const total = catState.questions.length;
    const answered = catState.questions.filter(q => q.selected_option_id !== null).length;
    const unanswered = total - answered;

    document.getElementById('fin-total').textContent = `${total} Soal`;
    document.getElementById('fin-answered').textContent = `${answered} Soal`;
    document.getElementById('fin-unanswered').textContent = `${unanswered} Soal`;

    document.getElementById('modal-finish-exam').classList.add('open');
}

function closeFinishModal() {
    document.getElementById('modal-finish-exam').classList.remove('open');
}

async function submitFinishExam() {
    const btn = document.getElementById('btn-confirm-finish');
    if (btn) {
        btn.disabled = true;
        btn.textContent = '⏳ Menghitung Skor & Diagnostic...';
    }

    const currentQ = catState.questions[catState.currentIndex];
    const lastDelta = getDeltaSecondsOnCurrentQuestion();
    const url = window.CAT_CONFIG.finishUrlTemplate.replace(':attemptId', catState.attemptId);

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.CAT_CONFIG.csrfToken
            },
            body: JSON.stringify({
                last_question_id: currentQ ? currentQ.question_id : null,
                last_delta_seconds: lastDelta
            })
        });

        const data = await res.json();
        if (data.status === 'success') {
            window.location.href = data.redirect_url;
        }
    } catch (e) {
        console.error(e);
        window.location.href = '/dashboard';
    }
}