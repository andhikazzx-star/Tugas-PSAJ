/**
 * landing.js – JavaScript khusus halaman landing e-Rapor
 * File ini terpisah dari app.js agar lebih mudah diubah oleh developer
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── 1. Efek Scroll Navbar ───────────────────────────────────
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            navbar.classList.toggle('scrolled', window.scrollY > 40);
        });
    }

    // ── 2. Animasi Fade-in saat Scroll ─────────────────────────
    const fadeEls = document.querySelectorAll('.fade-in-up');
    if (fadeEls.length > 0) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.12 });
        fadeEls.forEach(function (el) { observer.observe(el); });
    }

    // ── 3. Counter Angka Animasi ────────────────────────────────
    function animateCounter(el, target, duration) {
        duration = duration || 1800;
        var start = 0;
        var step = target / (duration / 16);
        var timer = setInterval(function () {
            start += step;
            if (start >= target) {
                start = target;
                clearInterval(timer);
            }
            el.textContent = Math.floor(start);
        }, 16);
    }

    // Ambil statistik dari API lalu jalankan counter
    var elSiswa = document.getElementById('count-siswa');
    var elKelas = document.getElementById('count-kelas');
    var elGuru = document.getElementById('count-guru');

    if (elSiswa && elKelas && elGuru) {
        fetch('?page=api.stats')
            .then(function (r) { return r.json(); })
            .catch(function () { return { siswa: 320, kelas: 18, guru: 42 }; })
            .then(function (data) {
                animateCounter(elSiswa, data.siswa || 320);
                animateCounter(elKelas, data.kelas || 18);
                animateCounter(elGuru, data.guru || 42);
            });
    }

    // ── 4. Smooth Scroll untuk link Anchor ─────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            e.preventDefault();
            var target = document.querySelector(a.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ── 5. Slider Fitur "Semua yang Anda Butuhkan" ─────────────
    var slider = document.getElementById('featuresSlider');
    var btnPrev = document.getElementById('sliderPrev');
    var btnNext = document.getElementById('sliderNext');
    var dotsContainer = document.getElementById('sliderDots');

    if (!slider || !btnPrev || !btnNext) return; // keluar jika elemen tidak ada

    var cards = slider.querySelectorAll('.feature-card');
    var totalCards = cards.length;
    var currentIndex = 0;

    // Hitung berapa kartu yang terlihat berdasarkan lebar layar
    function getVisibleCount() {
        if (window.innerWidth >= 1024) return 3;
        if (window.innerWidth >= 640) return 2;
        return 1;
    }

    // Hitung total "page" atau langkah slider
    function getTotalSteps() {
        return Math.max(0, totalCards - getVisibleCount());
    }

    // Buat dots indikator secara dinamis
    function buildDots() {
        if (!dotsContainer) return;
        dotsContainer.innerHTML = '';
        var steps = getTotalSteps();
        for (var i = 0; i <= steps; i++) {
            var dot = document.createElement('button');
            dot.className = 'slider-dot' + (i === 0 ? ' active' : '');
            dot.setAttribute('aria-label', 'Halaman ' + (i + 1));
            dot.dataset.index = i;
            dot.addEventListener('click', function () {
                goTo(parseInt(this.dataset.index));
            });
            dotsContainer.appendChild(dot);
        }
    }

    // Update tampilan dots
    function updateDots() {
        if (!dotsContainer) return;
        dotsContainer.querySelectorAll('.slider-dot').forEach(function (dot, idx) {
            dot.classList.toggle('active', idx === currentIndex);
        });
    }

    // Geser slider ke index tertentu
    function goTo(index) {
        var steps = getTotalSteps();
        currentIndex = Math.max(0, Math.min(index, steps));

        // Hitung offset: lebar satu kartu + gap (24px)
        var cardWidth = cards[0] ? cards[0].offsetWidth + 24 : 0;
        slider.style.transform = 'translateX(-' + (currentIndex * cardWidth) + 'px)';

        // Update state tombol
        btnPrev.disabled = currentIndex === 0;
        btnNext.disabled = currentIndex >= steps;

        updateDots();
    }

    // Event tombol
    btnPrev.addEventListener('click', function () { goTo(currentIndex - 1); });
    btnNext.addEventListener('click', function () { goTo(currentIndex + 1); });

    // Interval slide otomatis setiap 4 detik
    var autoSlide = setInterval(function () {
        var steps = getTotalSteps();
        if (currentIndex >= steps) {
            goTo(0); // kembali ke awal
        } else {
            goTo(currentIndex + 1);
        }
    }, 4000);

    // Hentikan auto-slide saat user hover
    slider.closest('.features-slider-wrapper').addEventListener('mouseenter', function () {
        clearInterval(autoSlide);
    });

    // Swipe touch untuk mobile
    var touchStartX = 0;
    slider.addEventListener('touchstart', function (e) {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    slider.addEventListener('touchend', function (e) {
        var diff = touchStartX - e.changedTouches[0].screenX;
        if (Math.abs(diff) > 50) {
            goTo(diff > 0 ? currentIndex + 1 : currentIndex - 1);
        }
    }, { passive: true });

    // Re-init saat layar diubah ukuran
    window.addEventListener('resize', function () {
        buildDots();
        goTo(0);
    });

    // Init pertama kali
    buildDots();
    goTo(0);

});
