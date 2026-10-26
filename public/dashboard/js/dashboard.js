/* ==========================================================================
   JAVASCRIPT DASHBOARD SISWA FORECASTING ACADEMY
   Lokasi: public/dashboard/js/dashboard.js
   ========================================================================== */
document.addEventListener('DOMContentLoaded', () => {

    /* 1. SISTEM NAVIGASI TAB INSTAN (SIDEBAR, BOTTOM NAV HP, & TOMBOL PINTAS) */
    const allTabButtons = document.querySelectorAll('[data-tab]');
    const allSwitchTriggers = document.querySelectorAll('[data-switch-tab]');
    const allTabPanes = document.querySelectorAll('.tab-pane');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    function activateTab(targetTabId) {
        allTabPanes.forEach(pane => {
            pane.classList.toggle('active', pane.id === targetTabId);
        });

        allTabButtons.forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-tab') === targetTabId);
        });

        // Tutup drawer sidebar otomatis jika di layar handphone
        if (window.innerWidth <= 820) {
            sidebar?.classList.remove('open');
            sidebarOverlay?.classList.remove('active');
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    allTabButtons.forEach(btn => {
        btn.addEventListener('click', () => activateTab(btn.getAttribute('data-tab')));
    });

    allSwitchTriggers.forEach(trigger => {
        trigger.addEventListener('click', () => activateTab(trigger.getAttribute('data-switch-tab')));
    });

    /* 2. DRAWER SIDEBAR UNTUK HANDPHONE */
    const btnOpenSidebar = document.getElementById('sidebar-open');
    const btnCloseSidebar = document.getElementById('sidebar-close');

    btnOpenSidebar?.addEventListener('click', () => {
        sidebar?.classList.add('open');
        sidebarOverlay?.classList.add('active');
    });

    function closeMobileSidebar() {
        sidebar?.classList.remove('open');
        sidebarOverlay?.classList.remove('active');
    }

    btnCloseSidebar?.addEventListener('click', closeMobileSidebar);
    sidebarOverlay?.addEventListener('click', closeMobileSidebar);

    /* 3. MODAL INTERAKTIF (SIMULASI CBT, PSIKOTES, & VIDEO MATERI) */
    const dashModal = document.getElementById('dash-modal');
    const dmTitle = document.getElementById('dm-title');
    const dmBody = document.getElementById('dm-body');
    const dmClose = document.getElementById('dash-modal-close');
    const dmOk = document.getElementById('dm-ok-btn');

    function showDashModal(title, message) {
        if (dmTitle) dmTitle.textContent = title;
        if (dmBody) dmBody.textContent = message;
        dashModal?.classList.add('active');
    }

    document.querySelectorAll('.trigger-info-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            showDashModal(btn.getAttribute('data-title'), btn.getAttribute('data-msg'));
        });
    });

    document.querySelectorAll('.trigger-video-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            const title = btn.getAttribute('data-title');
            showDashModal('🎬 Pemutar Video LMS: ' + title, 'Memuat player video materi pembelajaran beserta pelacak progres tontonan otomatis.');
        });
    });

    dmClose?.addEventListener('click', () => dashModal?.classList.remove('active'));
    dmOk?.addEventListener('click', () => dashModal?.classList.remove('active'));
    dashModal?.addEventListener('click', (e) => {
        if (e.target === dashModal) dashModal.classList.remove('active');
    });

    /* 4. CUSTOM CURSOR & EFEK PERCIKAN KLIK (SPARK + RIPPLE) */
    const cursorDot = document.getElementById('cursor-dot');
    const cursorOutline = document.getElementById('cursor-outline');
    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;
    let outlineX = mouseX;
    let outlineY = mouseY;

    window.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
        if (cursorDot) {
            cursorDot.style.left = `${mouseX}px`;
            cursorDot.style.top = `${mouseY}px`;
        }
    });

    function animateCursor() {
        outlineX += (mouseX - outlineX) * 0.18;
        outlineY += (mouseY - outlineY) * 0.18;
        if (cursorOutline) {
            cursorOutline.style.left = `${outlineX}px`;
            cursorOutline.style.top = `${outlineY}px`;
        }
        requestAnimationFrame(animateCursor);
    }
    animateCursor();

    document.querySelectorAll('a, button, [data-cursor-hover]').forEach(el => {
        el.addEventListener('mouseenter', () => cursorOutline?.classList.add('cursor-hover'));
        el.addEventListener('mouseleave', () => cursorOutline?.classList.remove('cursor-hover'));
    });

    const sparkColors = ['#2563EB', '#0EA5E9', '#F59E0B', '#10B981'];
    window.addEventListener('mousedown', (e) => {
        const ripple = document.createElement('div');
        ripple.className = 'click-ripple';
        ripple.style.left = `${e.clientX}px`;
        ripple.style.top = `${e.clientY}px`;
        document.body.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);

        for (let i = 0; i < 7; i++) {
            const spark = document.createElement('div');
            spark.className = 'click-spark';
            const angle = (Math.PI * 2 * i) / 7;
            const distance = 24 + Math.random() * 18;
            spark.style.left = `${e.clientX}px`;
            spark.style.top = `${e.clientY}px`;
            spark.style.backgroundColor = sparkColors[i % sparkColors.length];
            spark.style.setProperty('--dx', `${Math.cos(angle) * distance}px`);
            spark.style.setProperty('--dy', `${Math.sin(angle) * distance}px`);
            document.body.appendChild(spark);
            setTimeout(() => spark.remove(), 550);
        }
    });

    /* 5. ANIMASI PARTIKEL JARINGAN DI BACKGROUND CANVAS */
    const canvas = document.getElementById('bg-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let width = (canvas.width = window.innerWidth);
        let height = (canvas.height = window.innerHeight);

        window.addEventListener('resize', () => {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        });

        const particles = Array.from({ length: 32 }, () => ({
            x: Math.random() * width,
            y: Math.random() * height,
            vx: (Math.random() - 0.5) * 0.5,
            vy: (Math.random() - 0.5) * 0.5,
            radius: Math.random() * 2 + 1
        }));

        function drawParticles() {
            ctx.clearRect(0, 0, width, height);
            particles.forEach((p, i) => {
                p.x += p.vx;
                p.y += p.vy;
                if (p.x < 0 || p.x > width) p.vx *= -1;
                if (p.y < 0 || p.y > height) p.vy *= -1;

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(37, 99, 235, 0.18)';
                ctx.fill();

                for (let j = i + 1; j < particles.length; j++) {
                    const p2 = particles[j];
                    const dist = Math.hypot(p.x - p2.x, p.y - p2.y);
                    if (dist < 120) {
                        ctx.beginPath();
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(p2.x, p2.y);
                        ctx.strokeStyle = `rgba(37, 99, 235, ${0.09 * (1 - dist / 120)})`;
                        ctx.stroke();
                    }
                }
            });
            requestAnimationFrame(drawParticles);
        }
        drawParticles();
    }
});