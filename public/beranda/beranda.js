document.addEventListener('DOMContentLoaded', () => {

    /* ======================================================================
       1. MENU HAMBURGER NAVIGASI HANDPHONE (MOBILE DRAWER)
       ====================================================================== */
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const navLinks = document.getElementById('nav-links');

    if (mobileMenuToggle && navLinks) {
        mobileMenuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            mobileMenuToggle.classList.toggle('active');
            navLinks.classList.toggle('active');
        });

        // Tutup menu otomatis saat link navigasi diklik di HP
        navLinks.querySelectorAll('a, button').forEach((link) => {
            link.addEventListener('click', () => {
                mobileMenuToggle.classList.remove('active');
                navLinks.classList.remove('active');
            });
        });

        // Tutup menu saat area luar diklik
        document.addEventListener('click', (e) => {
            if (!navLinks.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                mobileMenuToggle.classList.remove('active');
                navLinks.classList.remove('active');
            }
        });
    }

    /* ======================================================================
       2. CUSTOM CURSOR INTERAKTIF & EFEK KLIK (RIPPLE + SPARK)
       ====================================================================== */
    const cursorDot = document.getElementById('cursor-dot');
    const cursorOutline = document.getElementById('cursor-outline');
    const isFinePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;
    let outlineX = mouseX;
    let outlineY = mouseY;

    if (isFinePointer) {
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

        const hoverTargets = document.querySelectorAll('a, button, [data-cursor-hover]');
        hoverTargets.forEach((el) => {
            el.addEventListener('mouseenter', () => cursorOutline?.classList.add('cursor-hover'));
            el.addEventListener('mouseleave', () => cursorOutline?.classList.remove('cursor-hover'));
        });
    }

    // Efek Klik/Sentuh (Ripple + Partikel Spark tetap jalan di Desktop & HP)
    const sparkColors = ['#2563EB', '#0EA5E9', '#F59E0B', '#10B981'];
    window.addEventListener('pointerdown', (e) => {
        cursorOutline?.classList.add('cursor-click');

        const ripple = document.createElement('div');
        ripple.className = 'click-ripple';
        ripple.style.left = `${e.clientX}px`;
        ripple.style.top = `${e.clientY}px`;
        document.body.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);

        for (let i = 0; i < 8; i++) {
            const spark = document.createElement('div');
            spark.className = 'click-spark';
            const angle = (Math.PI * 2 * i) / 8;
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

    window.addEventListener('pointerup', () => {
        cursorOutline?.classList.remove('cursor-click');
    });

    /* ======================================================================
       3. ANIMASI BACKGROUND CANVAS (OPTIMAL DI DESKTOP & RINGAN DI HP)
       ====================================================================== */
    const canvas = document.getElementById('bg-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let width = (canvas.width = window.innerWidth);
        let height = (canvas.height = window.innerHeight);

        window.addEventListener('resize', () => {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        });

        // Kurangi jumlah partikel di layar HP agar baterai & performa tetap hemat
        const particleCount = window.innerWidth < 768 ? 18 : 40;
        const particles = Array.from({ length: particleCount }, () => ({
            x: Math.random() * width,
            y: Math.random() * height,
            vx: (Math.random() - 0.5) * 0.55,
            vy: (Math.random() - 0.5) * 0.55,
            radius: Math.random() * 2 + 1.2
        }));

        function drawBackground() {
            ctx.clearRect(0, 0, width, height);

            particles.forEach((p, i) => {
                p.x += p.vx;
                p.y += p.vy;

                if (p.x < 0 || p.x > width) p.vx *= -1;
                if (p.y < 0 || p.y > height) p.vy *= -1;

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(37, 99, 235, 0.2)';
                ctx.fill();

                for (let j = i + 1; j < particles.length; j++) {
                    const p2 = particles[j];
                    const dist = Math.hypot(p.x - p2.x, p.y - p2.y);
                    if (dist < 125) {
                        ctx.beginPath();
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(p2.x, p2.y);
                        ctx.strokeStyle = `rgba(37, 99, 235, ${0.11 * (1 - dist / 125)})`;
                        ctx.lineWidth = 1;
                        ctx.stroke();
                    }
                }

                if (isFinePointer) {
                    const distMouse = Math.hypot(p.x - mouseX, p.y - mouseY);
                    if (distMouse < 150) {
                        ctx.beginPath();
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(mouseX, mouseY);
                        ctx.strokeStyle = `rgba(14, 165, 233, ${0.22 * (1 - distMouse / 150)})`;
                        ctx.lineWidth = 1.1;
                        ctx.stroke();
                    }
                }
            });

            requestAnimationFrame(drawBackground);
        }
        drawBackground();
    }

    /* ======================================================================
       4. FILTER KATEGORI PAKET PEMBELAJARAN
       ====================================================================== */
    const filterButtons = document.querySelectorAll('.filter-btn');
    const packageCards = document.querySelectorAll('.package-card');

    filterButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            filterButtons.forEach((b) => b.classList.remove('active'));
            btn.classList.add('active');

            const filterValue = btn.getAttribute('data-filter');
            packageCards.forEach((card) => {
                const category = card.getAttribute('data-category');
                if (filterValue === 'all' || category === filterValue) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    /* ======================================================================
       5. MODAL LOGIN GOOGLE & SCROLL ANIMATION
       ====================================================================== */
    const loginModal = document.getElementById('login-modal');
    const btnLoginTrigger = document.getElementById('btn-login-trigger');
    const modalClose = document.getElementById('modal-close');
    const modalSelectedPkg = document.getElementById('modal-selected-pkg');
    const pkgTriggers = document.querySelectorAll('.login-modal-trigger');

    function openModal(pkgName = null) {
        if (pkgName && modalSelectedPkg) {
            modalSelectedPkg.innerHTML = `Anda memilih <strong>${pkgName}</strong>. Silakan masuk dengan akun Google (Gmail) Anda untuk melanjutkan.`;
        } else if (modalSelectedPkg) {
            modalSelectedPkg.innerHTML = `Masuk secara instan menggunakan akun <strong>Google (Gmail)</strong> tanpa perlu repot menghafal password baru.`;
        }
        loginModal?.classList.add('active');
    }

    btnLoginTrigger?.addEventListener('click', () => openModal());
    pkgTriggers.forEach((btn) => {
        btn.addEventListener('click', () => openModal(btn.getAttribute('data-package')));
    });

    modalClose?.addEventListener('click', () => loginModal?.classList.remove('active'));
    loginModal?.addEventListener('click', (e) => {
        if (e.target === loginModal) loginModal.classList.remove('active');
    });

    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
            navbar?.classList.add('scrolled');
        } else {
            navbar?.classList.remove('scrolled');
        }
    });

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        },
        { threshold: 0.1 }
    );

    document.querySelectorAll('.reveal-up').forEach((el) => observer.observe(el));
});