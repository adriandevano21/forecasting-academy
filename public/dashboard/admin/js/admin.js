/* ==========================================================================
   JAVASCRIPT PANEL ADMIN BERBASIS POP-UP MODAL - FORECASTING ACADEMY
   Lokasi: public/dashboard/admin/js/admin.js
   ========================================================================== */
let currentTab = document.body.getAttribute('data-active-tab') || 'tab-overview';

document.addEventListener('DOMContentLoaded', () => {
    const tabBtns = document.querySelectorAll('[data-tab]');
    const goTabBtns = document.querySelectorAll('[data-go-tab]');
    const panes = document.querySelectorAll('.adm-pane');
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    function switchAdminTab(tabId) {
        currentTab = tabId;
        panes.forEach(p => p.classList.toggle('active', p.id === tabId));
        tabBtns.forEach(b => b.classList.toggle('active', b.getAttribute('data-tab') === tabId));

        if (window.innerWidth <= 768) {
            sidebar?.classList.remove('open');
            overlay?.classList.remove('active');
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    switchAdminTab(currentTab);

    tabBtns.forEach(btn => btn.addEventListener('click', () => switchAdminTab(btn.getAttribute('data-tab'))));
    goTabBtns.forEach(item => item.addEventListener('click', () => switchAdminTab(item.getAttribute('data-go-tab'))));

    document.getElementById('sb-open')?.addEventListener('click', () => {
        sidebar?.classList.add('open');
        overlay?.classList.add('active');
    });
    document.getElementById('sb-close')?.addEventListener('click', () => {
        sidebar?.classList.remove('open');
        overlay?.classList.remove('active');
    });
    overlay?.addEventListener('click', () => {
        sidebar?.classList.remove('open');
        overlay?.classList.remove('active');
    });

    // Tutup modal saat klik area gelap di luar kotak modal
    document.querySelectorAll('.adm-modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.remove('open');
        });
    });
});

function openModal(id) {
    document.getElementById(id)?.classList.add('open');
}

function closeModal(id) {
    document.getElementById(id)?.classList.remove('open');
}

function toggleStatus(table, id, column, tabId) {
    const form = document.getElementById('universal-toggle-form');
    document.getElementById('toggle-active-tab').value = tabId || currentTab;
    form.action = `/control-admin/toggle/${table}/${id}/${column}`;
    form.submit();
}

function deleteRecord(table, id, tabId) {
    if (!confirm(`Yakin ingin menghapus data ID #${id} dari tabel [${table}]?`)) return;
    const form = document.getElementById('universal-delete-form');
    document.getElementById('delete-active-tab').value = tabId || currentTab;
    form.action = `/control-admin/delete/${table}/${id}`;
    form.submit();
}

/* ==========================================================================
   FUNGSI PEMBUKA POP-UP MODAL (TAMBAH BARU & EDIT)
   ========================================================================== */

// 1. Modal Master Paket Tryout
function openTryoutModal(to = null) {
    document.getElementById('modal-tryout-title').textContent = to ? `✏️ Edit Paket Tryout: ${to.code}` : '➕ Tambah Master Paket Tryout Baru';
    document.getElementById('to_id').value = to ? to.id : '';
    document.getElementById('to_code').value = to ? (to.code || '') : '';
    document.getElementById('to_title').value = to ? to.title : '';
    if (to && to.program_id) document.getElementById('to_prog').value = to.program_id;
    document.getElementById('to_type').value = to ? to.type : 'full_skd';
    document.getElementById('to_dur').value = to ? to.duration_minutes : 60;
    document.getElementById('to_total').value = to ? to.total_questions : 20;
    document.getElementById('to_inst').value = to ? (to.instruction || '') : '';
    document.getElementById('to_active').checked = to ? !!to.is_active : true;
    openModal('modal-tryout');
}

// 2. Modal Bank Soal, Gambar Soal, Pembahasan & Gambar Opsi A-E
function openQuestionModal(q = null, defaultTryoutId = null, nextNum = 1) {
    document.getElementById('modal-question-title').textContent = q
        ? `✏️ Edit Soal No. ${q.question_number} (${q.tryout_code})`
        : `➕ Tambah Butir Soal Baru (No. ${nextNum})`;

    document.getElementById('q_id').value = q ? q.id : '';
    if (q) {
        document.getElementById('q_tryout').value = q.tryout_id;
        document.getElementById('q_number').value = q.question_number || 1;
        document.getElementById('q_topic').value = q.topic_id;
        document.getElementById('q_ideal').value = q.ideal_time_seconds;
        document.getElementById('q_img').value = q.question_image || '';
        document.getElementById('q_text').value = q.question_text;
        document.getElementById('q_disc').value = q.discussion_text || '';
        document.getElementById('q_disc_img').value = q.discussion_image || '';
        document.getElementById('q_vid').value = q.discussion_video_url || '';
    } else {
        if (defaultTryoutId) document.getElementById('q_tryout').value = defaultTryoutId;
        document.getElementById('q_number').value = nextNum || 1;
        document.getElementById('q_ideal').value = 50;
        ['q_img', 'q_text', 'q_disc', 'q_disc_img', 'q_vid'].forEach(id => document.getElementById(id).value = '');
    }

    ['A', 'B', 'C', 'D', 'E'].forEach(lbl => {
        document.getElementById(`opt_text_${lbl}`).value = '';
        document.getElementById(`opt_img_${lbl}`).value = '';
        document.getElementById(`opt_weight_${lbl}`).value = (!q && lbl === 'B') ? 5 : 0;
    });

    if (q && q.options) {
        q.options.forEach(op => {
            const txt = document.getElementById(`opt_text_${op.option_label}`);
            const img = document.getElementById(`opt_img_${op.option_label}`);
            const wgt = document.getElementById(`opt_weight_${op.option_label}`);
            if (txt) txt.value = op.option_text || '';
            if (img) img.value = op.option_image || '';
            if (wgt) wgt.value = op.score_weight;
        });
    }
    openModal('modal-question');
}

// 3. Modal Paket Belajar & Isi Bundling
/* ==========================================================================
   FUNGSI PAKET BELAJAR & BUNDLING MODAL (TAMBAH BARU & EDIT)
   ========================================================================== */

function openPackageModal(data = null) {
    const title = document.getElementById('modal-package-title');

    // Helper set nilai input
    const setValue = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val;
    };

    const setChecked = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.checked = Boolean(val);
    };

    // 1. Set Input Form Utama
    setValue('pkg_id', data ? (data.id || '') : '');
    setValue('pkg_name', data ? (data.name || '') : '');
    setValue('pkg_type', data ? (data.type || 'bundling') : 'bundling');
    setValue('pkg_badge', data ? (data.badge_text || '') : '');
    setValue('pkg_price', data ? (data.price || '') : '');
    setValue('pkg_disc', data ? (data.discount_price || '') : '');
    setValue('pkg_active_days', data ? (data.active_period_days || 365) : 365);
    setValue('pkg_desc', data ? (data.short_description || '') : '');
    setValue('pkg_features', data ? (data.features_text || '') : '');

    // Reset input file
    const thumbInput = document.getElementById('pkg_thumbnail');
    if (thumbInput) thumbInput.value = '';

    // 2. Set Pratinjau Thumbnail (Jika ada)
    const previewWrapper = document.getElementById('pkg_thumbnail_preview_wrapper');
    const previewImg = document.getElementById('pkg_thumbnail_preview');
    if (data && data.thumbnail && previewWrapper && previewImg) {
        previewImg.src = `/storage/${data.thumbnail}`;
        previewWrapper.style.display = 'block';
    } else if (previewWrapper) {
        previewWrapper.style.display = 'none';
    }

    // 3. Set Checkbox Boolean
    setChecked('pkg_homepage', data ? data.show_on_homepage : 1);
    setChecked('pkg_featured', data ? data.is_featured : 0);

    // 4. Set Select Item Bundling
    const setMultiSelect = (selectorName, selectedArray) => {
        const checkboxes = document.querySelectorAll(`input[name="${selectorName}"]`);
        checkboxes.forEach(chk => {
            const val = parseInt(chk.value);
            chk.checked = selectedArray ? selectedArray.includes(val) : false;
        });
    };

    setMultiSelect('tryout_ids[]', data ? data.tryout_ids : []);
    setMultiSelect('material_ids[]', data ? data.material_ids : []);
    setMultiSelect('mentor_ids[]', data ? data.mentor_ids : []);

    // 5. Judul & Buka Modal
    if (title) {
        title.textContent = data ? `✏️ Edit Paket Belajar: ${data.name}` : '➕ Buat Paket Belajar & Bundling Baru';
    }

    openModal('modal-package');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// 4. Modal Video Materi
function openMaterialModal(mt = null) {
    document.getElementById('modal-material-title').textContent = mt ? `✏️ Edit Materi: ${mt.title}` : '➕ Tambah Video Materi / Modul';
    document.getElementById('mat_id').value = mt ? mt.id : '';
    if (mt) document.getElementById('mat_topic').value = mt.topic_id;
    document.getElementById('mat_inst').value = mt ? (mt.instructor_id || '') : '';
    document.getElementById('mat_title').value = mt ? mt.title : '';
    document.getElementById('mat_type').value = mt ? mt.type : 'video';
    document.getElementById('mat_url').value = mt ? mt.content_url : '';
    document.getElementById('mat_dur').value = mt ? mt.duration : 900;
    document.getElementById('mat_free').checked = mt ? !!mt.is_free_preview : false;
    openModal('modal-material');
}

// 5. Modal Program, Subject, Topic
function openProgramModal(pr = null) {
    document.getElementById('modal-program-title').textContent = pr ? `✏️ Edit Program: ${pr.name}` : '➕ Tambah Program Baru';
    document.getElementById('prog_id').value = pr ? pr.id : '';
    document.getElementById('prog_name').value = pr ? pr.name : '';
    document.getElementById('prog_badge').value = pr ? (pr.badge_text || '') : '';
    document.getElementById('prog_desc').value = pr ? (pr.description || '') : '';
    document.getElementById('prog_sort').value = pr ? pr.sort_order : 1;
    document.getElementById('prog_home').checked = pr ? !!pr.show_on_homepage : true;
    openModal('modal-program');
}

function openSubjectModal(sb = null) {
    document.getElementById('modal-subject-title').textContent = sb ? `✏️ Edit Mata Uji: ${sb.name}` : '➕ Tambah Mata Uji Baru';
    document.getElementById('subj_id').value = sb ? sb.id : '';
    if (sb) document.getElementById('subj_prog').value = sb.program_id;
    document.getElementById('subj_name').value = sb ? sb.name : '';
    document.getElementById('subj_pg').value = sb ? sb.passing_grade : 65;
    document.getElementById('subj_disp').checked = sb ? !!sb.is_displayed : true;
    openModal('modal-subject');
}

function openTopicModal(tp = null) {
    document.getElementById('modal-topic-title').textContent = tp ? `✏️ Edit Sub-Topik: ${tp.name}` : '➕ Tambah Sub-Topik Baru';
    document.getElementById('top_id').value = tp ? tp.id : '';
    if (tp) document.getElementById('top_subj').value = tp.subject_id;
    document.getElementById('top_name').value = tp ? tp.name : '';
    document.getElementById('top_desc').value = tp ? (tp.description || '') : '';
    openModal('modal-topic');
}

// 6. Modal CMS Beranda
function openSettingModal(st = null) {
    document.getElementById('modal-setting-title').textContent = st ? `✏️ Edit Pengaturan: ${st.label}` : '➕ Tambah Pengaturan Beranda';
    document.getElementById('set_id').value = st ? st.id : '';
    document.getElementById('set_key').value = st ? st.key : '';
    document.getElementById('set_group').value = st ? st.group : 'hero';
    document.getElementById('set_label').value = st ? st.label : '';
    document.getElementById('set_value').value = st ? (st.value || '') : '';
    document.getElementById('set_disp').checked = st ? !!st.is_displayed : true;
    openModal('modal-setting');
}

function openMenuModal(mn = null) {
    document.getElementById('modal-menu-title').textContent = mn ? `✏️ Edit Menu: ${mn.title}` : '➕ Tambah Menu Navigasi';
    document.getElementById('menu_id').value = mn ? mn.id : '';
    document.getElementById('menu_title').value = mn ? mn.title : '';
    document.getElementById('menu_url').value = mn ? mn.url : '';
    document.getElementById('menu_location').value = mn ? mn.location : 'navbar';
    document.getElementById('menu_disp').checked = mn ? !!mn.is_displayed : true;
    openModal('modal-menu');
}

function openFeatureModal(ft = null) {
    document.getElementById('modal-feature-title').textContent = ft ? `✏️ Edit Fitur: ${ft.title}` : '➕ Tambah Fitur Unggulan';
    document.getElementById('feat_id').value = ft ? ft.id : '';
    document.getElementById('feat_title').value = ft ? ft.title : '';
    document.getElementById('feat_icon').value = ft ? ft.icon_key : 'chart';
    document.getElementById('feat_color').value = ft ? ft.color_theme : 'blue';
    document.getElementById('feat_desc').value = ft ? ft.description : '';
    document.getElementById('feat_disp').checked = ft ? !!ft.is_displayed : true;
    openModal('modal-feature');
}

// 7. Modal User, Transaksi, Akses & Diagnostik
function openUserModal(u = null) {
    document.getElementById('modal-user-title').textContent = u ? `✏️ Edit Pengguna: ${u.name}` : '➕ Tambah Pengguna Baru';
    document.getElementById('usr_id').value = u ? u.id : '';
    document.getElementById('usr_name').value = u ? u.name : '';
    document.getElementById('usr_email').value = u ? u.email : '';
    document.getElementById('usr_role').value = u ? u.role : 'student';
    openModal('modal-user');
}

function openTrxModal(tx = null) {
    document.getElementById('modal-trx-title').textContent = tx ? `✏️ Edit Transaksi: ${tx.invoice_code}` : '➕ Tambah Transaksi Baru';
    document.getElementById('trx_id').value = tx ? tx.id : '';
    if (tx) {
        document.getElementById('trx_user').value = tx.user_id;
        document.getElementById('trx_pkg').value = tx.package_id;
        document.getElementById('trx_amt').value = tx.total_amount;
        document.getElementById('trx_status').value = tx.payment_status;
    } else {
        document.getElementById('trx_amt').value = '';
    }
    openModal('modal-trx');
}

function openAccessModal(ua = null) {
    document.getElementById('modal-access-title').textContent = ua ? '✏️ Edit Masa Aktif Paket' : '➕ Beri Hak Akses Paket';
    document.getElementById('acc_id').value = ua ? ua.id : '';
    if (ua) {
        document.getElementById('acc_user').value = ua.user_id;
        document.getElementById('acc_pkg').value = ua.package_id;
        document.getElementById('acc_exp').value = (ua.expires_at || '').substring(0, 10);
    }
    openModal('modal-access');
}

function openDiagModal(dg = null) {
    document.getElementById('modal-diag-title').textContent = dg ? '✏️ Edit Competency Diagnostic' : '➕ Tambah Competency Diagnostic';
    document.getElementById('diag_id').value = dg ? dg.id : '';
    if (dg) {
        document.getElementById('diag_user').value = dg.user_id;
        document.getElementById('diag_topic').value = dg.topic_id;
        document.getElementById('diag_ans').value = dg.total_answered;
        document.getElementById('diag_cor').value = dg.total_correct;
        document.getElementById('diag_time').value = dg.total_time_seconds;
        document.getElementById('diag_status').value = dg.status_label;
    }
    openModal('modal-diag');
}