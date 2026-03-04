<?php
/**
 * views/landing/index.php
 * Halaman Landing – e-Rapor Sisipan
 *
 * CSS : public/css/landing.css
 * JS  : public/js/landing.js
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Rapor Sisipan – <?= SCHOOL_NAME ?></title>
    <meta name="description"
        content="Sistem Penilaian Digital <?= SCHOOL_NAME ?>. Input nilai, monitoring, hingga cetak rapor sisipan secara efisien dan terintegrasi.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- CSS Landing (edit di public/css/landing.css) -->
    <link rel="stylesheet" href="<?= asset('css/landing.css') ?>">
</head>

<body>

    <!-- ── Navbar ──────────────────────────────────────────────────── -->
    <nav class="navbar" id="navbar">
        <a href="?" class="navbar-brand">
            <img src="<?= asset('img/logo.png') ?>" alt="Logo SMKN 10 Surabaya" class="navbar-logo-img">
            <div class="navbar-name">
                <span class="navbar-title">e-Rapor Sisipan</span>
                <span class="navbar-sub"><?= htmlspecialchars(SCHOOL_NAME) ?></span>
            </div>
        </a>
        <div class="navbar-actions">
            <a href="?page=login" class="btn-nav-login">
                <i class="fas fa-sign-in-alt"></i>
                Masuk
            </a>
        </div>
    </nav>

    <!-- ── Hero ────────────────────────────────────────────────────── -->
    <section class="hero">
        <div class="hero-grid-overlay"></div>
        <div class="hero-circles">
            <div class="hero-circle"></div>
            <div class="hero-circle"></div>
            <div class="hero-circle"></div>
        </div>

        <div class="hero-content">

            <!-- Kiri: Teks utama -->
            <div>
                <div class="hero-badge">
                    <i class="fas fa-graduation-cap"></i>
                    Sistem Penilaian Digital
                </div>
                <h1 class="hero-headline">
                    Rapor Sisipan<br>
                    <span class="highlight">Lebih Cepat,</span><br>
                    Lebih Akurat.
                </h1>
                <p class="hero-description">
                    Platform manajemen penilaian digital untuk
                    <?= htmlspecialchars(SCHOOL_NAME) ?>.
                    Dari input nilai guru hingga cetak rapor sisipan — semua dalam satu sistem terintegrasi.
                </p>
                <div class="hero-actions">
                    <a href="?page=login" class="btn-hero-primary">
                        <i class="fas fa-sign-in-alt"></i>
                        Masuk ke Sistem
                    </a>
                    <a href="#fitur" class="btn-hero-secondary">
                        <i class="fas fa-play-circle"></i>
                        Pelajari Fitur
                    </a>
                </div>

                <div class="hero-stats">
                    <div class="hero-stat-item">
                        <div class="hero-stat-num">4</div>
                        <div class="hero-stat-label">Peran Pengguna</div>
                    </div>
                    <div class="hero-stat-item">
                        <div class="hero-stat-num">100%</div>
                        <div class="hero-stat-label">Berbasis Web</div>
                    </div>
                    <div class="hero-stat-item">
                        <div class="hero-stat-num">PDF</div>
                        <div class="hero-stat-label">Ekspor Rapor</div>
                    </div>
                </div>
            </div>



        </div><!-- .hero-content -->
    </section>

    <!-- ── Alur Kerja ───────────────────────────────────────────────── -->
    <section class="section section-light" id="alur">
        <div class="container">
            <div class="section-header fade-in-up" style="text-align:center;">
                <div class="section-label" style="display:inline-flex; margin-bottom:1rem;">
                    <i class="fas fa-route"></i> Alur Kerja
                </div>
                <h2 class="section-title" style="text-align:center;">Bagaimana Cara Kerjanya?</h2>
                <p class="section-subtitle" style="text-align:center; margin:0 auto;">
                    Proses input hingga cetak rapor yang terstruktur dan mudah diikuti oleh semua pihak.
                </p>
            </div>

            <div class="flow-steps">
                <div class="flow-step fade-in-up delay-1">
                    <div class="flow-num">1</div>
                    <i class="fas fa-database flow-step-icon"></i>
                    <div class="flow-step-title">Admin Setup</div>
                    <div class="flow-step-desc">Admin menyiapkan data kelas, siswa, mata pelajaran, dan mengassign guru
                        pengampu.</div>
                </div>
                <div class="flow-step fade-in-up delay-2">
                    <div class="flow-num">2</div>
                    <i class="fas fa-keyboard flow-step-icon"></i>
                    <div class="flow-step-title">Guru Input Nilai</div>
                    <div class="flow-step-desc">Guru mata pelajaran mengisi nilai pengetahuan &amp; keterampilan untuk
                        setiap siswa di kelasnya.</div>
                </div>
                <div class="flow-step fade-in-up delay-3">
                    <div class="flow-num">3</div>
                    <i class="fas fa-tasks flow-step-icon"></i>
                    <div class="flow-step-title">Wali Kelas Monitoring</div>
                    <div class="flow-step-desc">Wali kelas memantau progres, mengisi catatan sikap &amp;
                        ekstrakurikuler, lalu menginisiasi finalisasi.</div>
                </div>
                <div class="flow-step fade-in-up delay-4">
                    <div class="flow-num">4</div>
                    <i class="fas fa-file-pdf flow-step-icon"></i>
                    <div class="flow-step-title">Cetak Rapor PDF</div>
                    <div class="flow-step-desc">Rapor sisipan siap dicetak dalam format PDF profesional dan resmi untuk
                        seluruh siswa.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Fitur Unggulan (Slider) ──────────────────────────────────── -->
    <section class="section" id="fitur">
        <div class="container">
            <div class="section-header fade-in-up">
                <div class="section-label"><i class="fas fa-star"></i> Fitur Unggulan</div>
                <h2 class="section-title">Semua yang Anda Butuhkan</h2>
                <p class="section-subtitle">Dirancang khusus untuk memenuhi kebutuhan penilaian SMK yang kompleks dan
                    dinamis.</p>
            </div>

            <!-- Slider wrapper -->
            <div class="features-slider-wrapper">
                <div class="features-slider" id="featuresSlider">

                    <div class="feature-card">
                        <div class="feature-icon icon-green"><i class="fas fa-user-shield"></i></div>
                        <div class="feature-title">Multi-Role Access</div>
                        <p class="feature-desc">Sistem hak akses berlapis untuk Admin, Guru Mapel, Wali Kelas, dan
                            Kaprogli dengan tampilan yang disesuaikan.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon icon-blue"><i class="fas fa-chart-line"></i></div>
                        <div class="feature-title">Monitoring Real-time</div>
                        <p class="feature-desc">Dashboard interaktif yang menampilkan progres pengisian nilai secara
                            langsung untuk Wali Kelas dan Kaprogli.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon icon-amber"><i class="fas fa-file-import"></i></div>
                        <div class="feature-title">Impor Data Massal</div>
                        <p class="feature-desc">Input ratusan siswa dengan cepat menggunakan fitur bulk import. Cukup
                            copy-paste dari Excel, sistem yang memproses.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon icon-purple"><i class="fas fa-file-pdf"></i></div>
                        <div class="feature-title">Cetak Rapor PDF</div>
                        <p class="feature-desc">Cetak rapor sisipan per siswa maupun per kelas dalam format PDF yang
                            rapi dan siap ditandatangani.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon icon-teal"><i class="fas fa-lock"></i></div>
                        <div class="feature-title">Finalisasi &amp; Kunci Data</div>
                        <p class="feature-desc">Mekanisme finalisasi memastikan data nilai tidak bisa diubah setelah
                            rapor dikunci, menjaga integritas data.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon icon-red"><i class="fas fa-shield-alt"></i></div>
                        <div class="feature-title">Keamanan Berlapis</div>
                        <p class="feature-desc">Proteksi CSRF, enkripsi password, serta validasi sesi memastikan data
                            penilaian selalu aman.</p>
                    </div>

                </div><!-- .features-slider -->
            </div><!-- .features-slider-wrapper -->

            <!-- Navigasi slider: tombol & dots -->
            <div class="slider-nav">
                <button class="slider-btn" id="sliderPrev" aria-label="Sebelumnya">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="slider-dots" id="sliderDots"></div>
                <button class="slider-btn" id="sliderNext" aria-label="Berikutnya">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

        </div>
    </section>

    <!-- ── Peran Pengguna ────────────────────────────────────────────── -->
    <section class="section section-dark" id="peran">
        <div class="container">
            <div class="section-header fade-in-up">
                <div class="section-label light-mode"><i class="fas fa-users"></i> Peran Pengguna</div>
                <h2 class="section-title white">Empat Peran, Satu Tujuan</h2>
                <p class="section-subtitle white">Setiap pengguna memiliki akses dan wewenang yang terstruktur sesuai
                    perannya.</p>
            </div>
            <div class="roles-grid">
                <div class="role-card fade-in-up delay-1">
                    <div class="role-icon" style="background:rgba(46,125,50,0.2); color:#81c784;">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <div class="role-name">Admin</div>
                    <div class="role-caption">Mengelola seluruh infrastruktur sistem penilaian.</div>
                    <ul class="role-abilities">
                        <li><i class="fas fa-check-circle"></i>Manajemen User &amp; Hak Akses</li>
                        <li><i class="fas fa-check-circle"></i>Data Kelas, Siswa, Mata Pelajaran</li>
                        <li><i class="fas fa-check-circle"></i>Plotting Guru Pengampuan</li>
                        <li><i class="fas fa-check-circle"></i>Proses Tahun Ajaran Baru</li>
                    </ul>
                </div>
                <div class="role-card fade-in-up delay-2">
                    <div class="role-icon" style="background:rgba(30,136,168,0.2); color:#4fc3f7;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="role-name">Guru Mata Pelajaran</div>
                    <div class="role-caption">Fokus pada pengisian nilai untuk kelas yang diampu.</div>
                    <ul class="role-abilities">
                        <li><i class="fas fa-check-circle"></i>Lihat Daftar Pengampuan</li>
                        <li><i class="fas fa-check-circle"></i>Input Nilai Per Siswa</li>
                        <li><i class="fas fa-check-circle"></i>Simpan Draft &amp; Tandai Selesai</li>
                    </ul>
                </div>
                <div class="role-card fade-in-up delay-3">
                    <div class="role-icon" style="background:rgba(251,192,45,0.15); color:#FBC02D;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="role-name">Wali Kelas</div>
                    <div class="role-caption">Koordinator nilai dan pencetak rapor untuk kelasnya.</div>
                    <ul class="role-abilities">
                        <li><i class="fas fa-check-circle"></i>Monitoring Progres Nilai</li>
                        <li><i class="fas fa-check-circle"></i>Input Catatan &amp; Ekstrakurikuler</li>
                        <li><i class="fas fa-check-circle"></i>Finalisasi &amp; Cetak Rapor</li>
                    </ul>
                </div>
                <div class="role-card fade-in-up delay-4">
                    <div class="role-icon" style="background:rgba(106,27,154,0.2); color:#ce93d8;">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <div class="role-name">Kaprogli</div>
                    <div class="role-caption">Pengawas keseluruhan capaian nilai per jurusan.</div>
                    <ul class="role-abilities">
                        <li><i class="fas fa-check-circle"></i>Monitoring Seluruh Kelas di Jurusan</li>
                        <li><i class="fas fa-check-circle"></i>Lihat Rekap Progress Pengisian</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ── CTA ──────────────────────────────────────────────────────── -->
    <section class="cta-section">
        <div class="cta-inner fade-in-up">
            <h2 class="cta-title">Siap Memulai?</h2>
            <p class="cta-sub">Masuk ke sistem dan mulai kelola penilaian sisipan secara efisien.</p>
            <a href="?page=login" class="btn-hero-primary" style="display:inline-flex;">
                <i class="fas fa-sign-in-alt"></i>
                Masuk ke e-Rapor
            </a>
        </div>
    </section>

    <!-- ── Footer ───────────────────────────────────────────────────── -->
    <footer class="footer">
        <p class="footer-text">
            &copy; <?= date('Y') ?>
            <strong style="color:rgba(255,255,255,0.5)"><?= htmlspecialchars(SCHOOL_NAME) ?></strong>
            – <?= htmlspecialchars(APP_NAME) ?> v<?= APP_VERSION ?>.
        </p>
    </footer>

    <!-- JavaScript Landing (edit di public/js/landing.js) -->
    <script src="<?= asset('js/landing.js') ?>"></script>

</body>

</html>