/* ==========================================================================
   JAVASCRIPT ANALISIS TRYOUT (TOP NAVBAR TABS + INTERACTIVE PER-SOAL VIEWER)
   Lokasi: public/dashboard/js/analysis.js
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    /* 1. NAVIGASI TAB NAVBAR ATAS */
    const navTabs = document.querySelectorAll('.an-nav-tab[data-an-tab]');
    const tabPanels = document.querySelectorAll('.an-tab-panel');

    function activateTab(panelId) {
        navTabs.forEach(t => {
            t.classList.toggle('active', t.getAttribute('data-an-tab') === panelId);
        });
        tabPanels.forEach(p => {
            p.classList.toggle('active', p.id === panelId);
        });
    }

    navTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            activateTab(tab.getAttribute('data-an-tab'));
        });
    });

    // Tombol pintasan antar tab
    document.querySelectorAll('.js-switch-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-target-tab');
            if (target) activateTab(target);
        });
    });

    /* 2. WORKSPACE BEDAH SOAL PER BUTIR (1 SOAL PER TAMPILAN) */
    const slides = Array.from(document.querySelectorAll('.an-q-slide'));
    const matrixBtns = Array.from(document.querySelectorAll('.matrix-btn'));
    const filterPills = document.querySelectorAll('#q-status-filters [data-q-filter]');
    const searchInput = document.getElementById('q-search-input');
    const btnPrev = document.getElementById('btn-prev-q');
    const btnNext = document.getElementById('btn-next-q');
    const posIndicator = document.getElementById('active-q-position');
    const emptyBox = document.getElementById('q-filter-empty');
    const btnResetFilter = document.getElementById('btn-reset-q-filter');

    let currentStatusFilter = 'all';
    let currentSlideIndex = 0;
    let filteredIndices = slides.map((_, i) => i);

    function computeFilteredIndices() {
        const q = (searchInput?.value || '').trim().toLowerCase();
        filteredIndices = [];

        slides.forEach((slide, idx) => {
            const status = slide.getAttribute('data-status');
            const isOvertime = slide.getAttribute('data-overtime') === '1';
            const isDoubtful = slide.getAttribute('data-doubtful') === '1';
            const hay = slide.getAttribute('data-search') || '';

            let matchFilter = true;
            if (currentStatusFilter === 'correct') matchFilter = (status === 'correct');
            else if (currentStatusFilter === 'partial') matchFilter = (status === 'partial');
            else if (currentStatusFilter === 'wrong') matchFilter = (status === 'wrong');
            else if (currentStatusFilter === 'empty') matchFilter = (status === 'empty');
            else if (currentStatusFilter === 'overtime') matchFilter = isOvertime;
            else if (currentStatusFilter === 'doubtful') matchFilter = isDoubtful;

            const matchSearch = !q || hay.includes(q);
            const passed = matchFilter && matchSearch;

            if (passed) filteredIndices.push(idx);

            if (matrixBtns[idx]) {
                matrixBtns[idx].style.opacity = passed ? '1' : '0.22';
            }
        });

        if (filteredIndices.length > 0 && !filteredIndices.includes(currentSlideIndex)) {
            currentSlideIndex = filteredIndices[0];
        }

        renderActiveSlide();
    }

    function renderActiveSlide() {
        if ( slides.length === 0 ) return;

        if (filteredIndices.length === 0) {
            slides.forEach(s => s.classList.remove('is-active'));
            if (emptyBox) emptyBox.style.display = 'block';
            if (posIndicator) posIndicator.textContent = '0 Soal ditemukan';
            if (btnPrev) btnPrev.disabled = true;
            if (btnNext) btnNext.disabled = true;
            return;
        }

        if (emptyBox) emptyBox.style.display = 'none';

        slides.forEach((s, idx) => {
            s.classList.toggle('is-active', idx === currentSlideIndex);
        });

        matrixBtns.forEach((b, idx) => {
            b.classList.toggle('is-current', idx === currentSlideIndex);
        });

        const posInFiltered = filteredIndices.indexOf(currentSlideIndex);
        const qNum = slides[currentSlideIndex]?.getAttribute('data-q-num') || (currentSlideIndex + 1);

        if (posIndicator) {
            posIndicator.textContent = `Soal #${qNum} (${posInFiltered + 1} dari ${filteredIndices.length} soal)`;
        }

        if (btnPrev) btnPrev.disabled = (posInFiltered <= 0);
        if (btnNext) btnNext.disabled = (posInFiltered >= filteredIndices.length - 1);
    }

    // Klik Nomor Soal di Matrix Kanan
    matrixBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetIdx = parseInt(btn.getAttribute('data-goto-index') || '0', 10);

            // Jika nomor yang diklik sedang terfilter keluar, reset filter otomatis
            if (!filteredIndices.includes(targetIdx)) {
                currentStatusFilter = 'all';
                if (searchInput) searchInput.value = '';
                filterPills.forEach(p => p.classList.toggle('active', p.getAttribute('data-q-filter') === 'all'));
                computeFilteredIndices();
            }

            currentSlideIndex = targetIdx;
            renderActiveSlide();
        });
    });

    // Tombol Prev & Next
    btnPrev?.addEventListener('click', () => {
        const pos = filteredIndices.indexOf(currentSlideIndex);
        if (pos > 0) {
            currentSlideIndex = filteredIndices[pos - 1];
            renderActiveSlide();
        }
    });

    btnNext?.addEventListener('click', () => {
        const pos = filteredIndices.indexOf(currentSlideIndex);
        if (pos !== -1 && pos < filteredIndices.length - 1) {
            currentSlideIndex = filteredIndices[pos + 1];
            renderActiveSlide();
        }
    });

    // Navigasi Keyboard Panah Kiri & Kanan saat Tab Bedah Soal Aktif
    document.addEventListener('keydown', (e) => {
        const qPanel = document.getElementById('panel-questions');
        if (!qPanel || !qPanel.classList.contains('active')) return;
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName)) return;

        if (e.key === 'ArrowLeft') {
            btnPrev?.click();
        } else if (e.key === 'ArrowRight') {
            btnNext?.click();
        }
    });

    // Filter Status Soal
    filterPills.forEach(pill => {
        pill.addEventListener('click', () => {
            filterPills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            currentStatusFilter = pill.getAttribute('data-q-filter') || 'all';
            computeFilteredIndices();
        });
    });

    searchInput?.addEventListener('input', computeFilteredIndices);

    btnResetFilter?.addEventListener('click', () => {
        currentStatusFilter = 'all';
        if (searchInput) searchInput.value = '';
        filterPills.forEach(p => p.classList.toggle('active', p.getAttribute('data-q-filter') === 'all'));
        computeFilteredIndices();
    });

    // Pintasan dari Tab Ringkasan langsung membuka Soal Salah / Overtime
    document.querySelectorAll('.js-open-filtered-questions').forEach(btn => {
        btn.addEventListener('click', () => {
            const filterKey = btn.getAttribute('data-filter') || 'all';
            activateTab('panel-questions');
            currentStatusFilter = filterKey;
            filterPills.forEach(p => p.classList.toggle('active', p.getAttribute('data-q-filter') === filterKey));
            computeFilteredIndices();
        });
    });

    // Pintasan dari Tabel Audit Waktu ("🔍 Bedah Soal") langsung membuka nomor soal terkait di Tab 2
    document.querySelectorAll('.js-inspect-question').forEach(btn => {
        btn.addEventListener('click', () => {
            const idx = parseInt(btn.getAttribute('data-inspect-index') || '0', 10);
            activateTab('panel-questions');
            currentStatusFilter = 'all';
            if (searchInput) searchInput.value = '';
            filterPills.forEach(p => p.classList.toggle('active', p.getAttribute('data-q-filter') === 'all'));
            computeFilteredIndices();
            currentSlideIndex = idx;
            renderActiveSlide();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    /* 3. SALIN PEMBAHASAN */
    document.querySelectorAll('[data-copy-target]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-copy-target');
            const el = document.getElementById(id);
            if (!el) return;

            navigator.clipboard.writeText(el.innerText.trim()).then(() => {
                const prev = btn.textContent;
                btn.textContent = '✓ Tersalin!';
                setTimeout(() => { btn.textContent = prev; }, 1400);
            });
        });
    });

    /* 4. FILTER TABEL AUDIT WAKTU (TAB 4) */
    const logFilterBtns = document.querySelectorAll('[data-filter-log]');
    const logRows = document.querySelectorAll('#question-log-table tbody tr');

    logFilterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            logFilterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const type = btn.getAttribute('data-filter-log');

            logRows.forEach(row => {
                const rowType = row.getAttribute('data-log-type');
                row.style.display = (type === 'all' || rowType === type) ? '' : 'none';
            });
        });
    });

    /* 5. MODAL ZOOM GAMBAR & VIDEO PEMBAHASAN */
    const imgModal = document.getElementById('an-image-modal');
    const imgTarget = document.getElementById('an-img-modal-target');
    const imgTitle = document.getElementById('an-img-modal-title');
    const imgClose = document.getElementById('an-img-modal-close');

    document.querySelectorAll('.an-zoomable-img').forEach(img => {
        img.addEventListener('click', () => {
            if (imgTarget) imgTarget.src = img.src;
            if (imgTitle) imgTitle.textContent = img.getAttribute('data-img-title') || 'Pratinjau Gambar';
            imgModal?.classList.add('open');
        });
    });

    imgClose?.addEventListener('click', () => imgModal?.classList.remove('open'));
    imgModal?.addEventListener('click', (e) => {
        if (e.target === imgModal) imgModal.classList.remove('open');
    });

    const vidModal = document.getElementById('an-video-modal');
    const vidTitle = document.getElementById('an-vid-modal-title');
    const vidContainer = document.getElementById('an-video-container');
    const vidClose = document.getElementById('an-vid-modal-close');

    function toYoutubeEmbed(url) {
        if (!url) return '';
        const match = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i);
        return match && match[1] ? `https://www.youtube.com/embed/${match[1]}?autoplay=1` : url;
    }

    document.querySelectorAll('.trigger-disc-video').forEach(btn => {
        btn.addEventListener('click', () => {
            const url = toYoutubeEmbed(btn.getAttribute('data-video-url') || '');
            const title = btn.getAttribute('data-video-title') || 'Video Pembahasan';
            if (vidTitle) vidTitle.textContent = '🎬 ' + title;
            if (vidContainer) {
                vidContainer.innerHTML = url
                    ? `<iframe src="${url}" allow="autoplay; encrypted-media" allowfullscreen></iframe>`
                    : `<div style="color:#fff;padding:40px;text-align:center;">Video pembahasan sedang disiapkan oleh Mentor.</div>`;
            }
            vidModal?.classList.add('open');
        });
    });

    function closeVid() {
        vidModal?.classList.remove('open');
        if (vidContainer) vidContainer.innerHTML = '';
    }

    vidClose?.addEventListener('click', closeVid);
    vidModal?.addEventListener('click', (e) => {
        if (e.target === vidModal) closeVid();
    });
});