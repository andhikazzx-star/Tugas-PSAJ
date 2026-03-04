<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= e($title ?? APP_NAME) ?>
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-logo">
                    <img src="<?= asset('img/logo.png') ?>" alt="Logo" style="width:28px; height:auto;">
                </div>
                <div class="brand-text">
                    <div class="brand-title">E-Rapor Sisipan</div>
                    <div class="brand-sub">SMK NEGERI 10 SURABAYA</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-label">Menu Utama</div>
                <a href="?page=dashboard" class="nav-item <?= get_param('page') === 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>

                <?php if (Session::hasRole(ROLE_GURU_MAPEL)): ?>
                    <div class="nav-section-label">Guru</div>
                    <a href="?page=nilai" class="nav-item <?= strpos(get_param('page'), 'nilai') === 0 ? 'active' : '' ?>">
                        <i class="fas fa-edit"></i>
                        <span>Input Nilai</span>
                    </a>
                <?php endif; ?>

                <?php if (Session::hasRole(ROLE_WALI_KELAS)): ?>
                    <div class="nav-section-label">Wali Kelas</div>
                    <a href="?page=monitoring" class="nav-item <?= get_param('page') === 'monitoring' ? 'active' : '' ?>">
                        <i class="fas fa-file-invoice"></i>
                        <span>Kelola Rapor Sisipan</span>
                    </a>
                <?php endif; ?>

                <?php if (Session::hasRole(ROLE_KAPROGLI)): ?>
                    <div class="nav-section-label">Kaprogli</div>
                    <a href="?page=monitoring_kaprogli"
                        class="nav-item <?= get_param('page') === 'monitoring_kaprogli' ? 'active' : '' ?>">
                        <i class="fas fa-chart-line"></i>
                        <span>Monitoring Jurusan</span>
                    </a>
                <?php endif; ?>

                <?php if (Session::hasRole(ROLE_ADMIN)): ?>
                    <div class="nav-section-label">Administrator</div>
                    <a href="?page=admin.users"
                        class="nav-item <?= strpos(get_param('page'), 'admin.users') === 0 ? 'active' : '' ?>">
                        <i class="fas fa-users-cog"></i>
                        <span>Data Pengguna</span>
                    </a>
                    <a href="?page=admin.jurusan"
                        class="nav-item <?= strpos(get_param('page'), 'admin.jurusan') === 0 ? 'active' : '' ?>">
                        <i class="fas fa-sitemap"></i>
                        <span>Data Jurusan</span>
                    </a>
                    <a href="?page=admin.kelas"
                        class="nav-item <?= strpos(get_param('page'), 'admin.kelas') === 0 ? 'active' : '' ?>">
                        <i class="fas fa-chalkboard"></i>
                        <span>Data Kelas</span>
                    </a>
                    <a href="?page=admin.mapel"
                        class="nav-item <?= strpos(get_param('page'), 'admin.mapel') === 0 ? 'active' : '' ?>">
                        <i class="fas fa-book"></i>
                        <span>Mata Pelajaran</span>
                    </a>
                    <a href="?page=admin.siswa"
                        class="nav-item <?= strpos(get_param('page'), 'admin.siswa') === 0 ? 'active' : '' ?>">
                        <i class="fas fa-user-graduate"></i>
                        <span>Data Siswa</span>
                    </a>
                    <a href="?page=admin.pengampuan"
                        class="nav-item <?= strpos(get_param('page'), 'admin.pengampuan') === 0 ? 'active' : '' ?>">
                        <i class="fas fa-hands-helping"></i>
                        <span>Guru Mapel</span>
                    </a>
                    <a href="?page=admin.tahun_ajaran_baru"
                        class="nav-item <?= strpos(get_param('page'), 'admin.tahun_ajaran_baru') === 0 ? 'active' : '' ?>">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Tahun Ajaran Baru</span>
                    </a>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr(Session::get('user_name', 'U'), 0, 1)) ?>
                    </div>
                    <div class="user-detail">
                        <span class="user-name">
                            <?= e(Session::get('user_name')) ?>
                        </span>
                        <span class="user-role">
                            <?= e(implode(', ', array_map('ucfirst', Session::getUserRoles()))) ?>
                        </span>
                    </div>
                </div>
                <button type="button" class="btn-logout" onclick="location.href='?page=logout'" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="topbar">
                <button type="button" class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="topbar-center">
                    <h1 class="page-title">
                        <?= e($pageTitle ?? 'Dashboard') ?>
                    </h1>
                </div>
                <div class="topbar-right">
                    <!-- Notifications -->
                    <div class="notif-wrapper">
                        <button type="button" class="notif-btn" id="notifBtn">
                            <i class="fas fa-bell"></i>
                            <?php if (($unread ?? 0) > 0): ?>
                                <span class="notif-badge">
                                    <?= $unread ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        <div class="notif-dropdown" id="notifDropdown">
                            <div class="notif-header">
                                <span>Notifikasi</span>
                                <a href="?page=dashboard&mark_read=1" class="notif-mark-read">Tandai semua dibaca</a>
                            </div>
                            <div class="notif-body">
                                <?php if (!empty($notifications)): ?>
                                    <?php foreach ($notifications as $n): ?>
                                        <div class="notif-item <?= $n['is_read'] ? '' : 'unread' ?>">
                                            <i class="fas fa-info-circle"></i>
                                            <div>
                                                <p>
                                                    <?= e($n['message']) ?>
                                                </p>
                                                <small>
                                                    <?= formatDate($n['created_at']) ?>
                                                </small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="notif-empty">Tidak ada notifikasi baru</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <div class="flash-container">
                <?php if (Session::hasFlash('success')): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <?= e(Session::getFlash('success')) ?>
                        </div>
                        <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                    </div>
                <?php endif; ?>
                <?php if (Session::hasFlash('error')): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <?= e(Session::getFlash('error')) ?>
                        </div>
                        <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                    </div>
                <?php endif; ?>
                <?php if (Session::hasFlash('warning')): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <?= e(Session::getFlash('warning')) ?>
                        </div>
                        <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                    </div>
                <?php endif; ?>
            </div>

            <section class="page-content">
                <?= $content ?>
            </section>

            <footer class="page-footer">
                &copy;
                <?= date('Y') ?>
                <?= SCHOOL_NAME ?> – e-Rapor Sisipan v
                <?= APP_VERSION ?>
            </footer>
        </main>
    </div>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>

</html>