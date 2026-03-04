<?php
$pageTitle = 'Dashboard';
ob_start();
?>

<div class="dashboard-grid">

    <?php if (in_array(ROLE_ADMIN, $roles)): ?>
        <!-- ===== ADMIN STATS ===== -->
        <section class="section-full">
            <div class="section-header">
                <h2><i class="fas fa-tachometer-alt"></i> Ringkasan Sistem</h2>
                <span class="badge badge-info">Administrator</span>
            </div>
            <div class="stats-grid">
                <div class="stat-card stat-green">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-body">
                        <div class="stat-number">
                            <?= e($stat_users ?? 0) ?>
                        </div>
                        <div class="stat-label">Total Pengguna</div>
                    </div>
                </div>
                <div class="stat-card stat-blue">
                    <div class="stat-icon"><i class="fas fa-chalkboard"></i></div>
                    <div class="stat-body">
                        <div class="stat-number">
                            <?= e($stat_kelas ?? 0) ?>
                        </div>
                        <div class="stat-label">Total Kelas</div>
                    </div>
                </div>
                <div class="stat-card stat-orange">
                    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="stat-body">
                        <div class="stat-number">
                            <?= e($stat_siswa ?? 0) ?>
                        </div>
                        <div class="stat-label">Total Siswa</div>
                    </div>
                </div>
                <div class="stat-card stat-purple">
                    <div class="stat-icon"><i class="fas fa-check-double"></i></div>
                    <div class="stat-body">
                        <div class="stat-number">
                            <?= e($stat_final ?? 0) ?>
                        </div>
                        <div class="stat-label">Kelas Difinalisasi</div>
                    </div>
                </div>
                <div class="stat-card stat-teal">
                    <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="stat-body">
                        <div class="stat-number">
                            <?= e($stat_pengampuan ?? 0) ?>
                        </div>
                        <div class="stat-label">Total Pengampuan</div>
                    </div>
                </div>
            </div>

            <div class="quick-links">
                <h3>Akses Cepat</h3>
                <div class="quick-grid">
                    <a href="?page=admin.users" class="quick-card">
                        <i class="fas fa-users-cog"></i>
                        <span>Kelola Pengguna</span>
                    </a>
                    <a href="?page=admin.kelas" class="quick-card">
                        <i class="fas fa-chalkboard"></i>
                        <span>Kelola Kelas</span>
                    </a>
                    <a href="?page=admin.siswa" class="quick-card">
                        <i class="fas fa-user-graduate"></i>
                        <span>Kelola Siswa</span>
                    </a>
                    <a href="?page=admin.pengampuan" class="quick-card">
                        <i class="fas fa-book-open"></i>
                        <span>Kelola Pengampuan</span>
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if (in_array(ROLE_GURU_MAPEL, $roles)): ?>
        <!-- ===== GURU MAPEL ===== -->
        <section class="section-full">
            <div class="section-header">
                <h2><i class="fas fa-edit"></i> Pengampuan Saya</h2>
                <span class="badge badge-warning">Guru Mata Pelajaran</span>
            </div>
            <?php if (!empty($pengampuan_list)): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th>T.A.</th>
                                <th>Status Kelas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pengampuan_list as $p): ?>
                                <tr>
                                    <td><strong>
                                            <?= e($p['mapel_nama']) ?>
                                        </strong></td>
                                    <td>
                                        <?= e($p['kelas_nama']) ?>
                                    </td>
                                    <td>
                                        <?= e($p['jurusan_nama']) ?>
                                    </td>
                                    <td>
                                        <?= e($p['tahun_ajaran']) ?>
                                    </td>
                                    <td>
                                        <?= statusKelasBadge($p['kelas_status']) ?>
                                    </td>
                                    <td>
                                        <?php if ($p['kelas_status'] !== 'final'): ?>
                                            <a href="?page=nilai&pengampuan_id=<?= $p['id'] ?>&semester=1"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Input Nilai
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted"><i class="fas fa-lock"></i> Difinalisasi</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>Belum ada pengampuan yang ditugaskan kepada Anda.</p>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <?php if (in_array(ROLE_WALI_KELAS, $roles)): ?>
        <!-- ===== WALI KELAS ===== -->
        <section class="section-full">
            <div class="section-header">
                <h2><i class="fas fa-chalkboard"></i> Kelas yang Saya Wali</h2>
                <span class="badge badge-success">Wali Kelas</span>
            </div>
            <?php if (!empty($kelas_wali_list)): ?>
                <div class="kelas-card-grid">
                    <?php foreach ($kelas_wali_list as $k): ?>
                        <div class="kelas-summary-card <?= $k['status'] === 'final' ? 'card-final' : '' ?>">
                            <div class="kelas-card-header">
                                <div>
                                    <h3>
                                        <?= e($k['nama']) ?>
                                    </h3>
                                    <p>
                                        <?= e($k['jurusan_nama']) ?> – T.A.
                                        <?= e($k['tahun_ajaran']) ?>
                                    </p>
                                </div>
                                <?= statusKelasBadge($k['status']) ?>
                            </div>
                            <div class="kelas-card-stats">
                                <div class="mini-stat">
                                    <i class="fas fa-users"></i>
                                    <span>
                                        <?= e($k['total_siswa']) ?> Siswa
                                    </span>
                                </div>
                            </div>
                            <div class="kelas-card-actions">
                                <a href="?page=monitoring&kelas_id=<?= $k['id'] ?>&semester=1" class="btn btn-sm btn-primary">
                                    <i class="fas fa-chart-bar"></i> Lihat Monitoring
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>Belum ada kelas yang Anda wali.</p>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <?php if (in_array(ROLE_KAPROGLI, $roles)): ?>
        <!-- ===== KAPROGLI ===== -->
        <section class="section-full">
            <div class="section-header">
                <h2><i class="fas fa-sitemap"></i> Jurusan yang Saya Kelola</h2>
                <span class="badge badge-info">Kaprogli</span>
            </div>
            <?php if (!empty($jurusan_list)): ?>
                <div class="kelas-card-grid">
                    <?php foreach ($jurusan_list as $j): ?>
                        <div class="kelas-summary-card">
                            <div class="kelas-card-header">
                                <div>
                                    <h3>
                                        <?= e($j['nama']) ?>
                                    </h3>
                                    <p>
                                        <?= e($j['total_kelas']) ?> Kelas Aktif
                                    </p>
                                </div>
                                <i class="fas fa-sitemap fa-2x" style="color:var(--primary)"></i>
                            </div>
                            <div class="kelas-card-actions">
                                <a href="?page=monitoring_kaprogli&jurusan_id=<?= $j['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-chart-bar"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>Belum ada jurusan yang ditugaskan kepada Anda.</p>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <!-- Notifikasi terbaru -->
    <?php if (!empty($notifications)): ?>
        <section class="section-full">
            <div class="section-header">
                <h2><i class="fas fa-bell"></i> Notifikasi Terbaru</h2>
            </div>
            <div class="notif-list-full">
                <?php foreach ($notifications as $notif): ?>
                    <div class="notif-item-full <?= $notif['is_read'] ? '' : 'unread' ?>">
                        <div class="notif-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="notif-content">
                            <p>
                                <?= e($notif['message']) ?>
                            </p>
                            <small>
                                <?= formatDate($notif['created_at']) ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

</div><!-- .dashboard-grid -->

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>