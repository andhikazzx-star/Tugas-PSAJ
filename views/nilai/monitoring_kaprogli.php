<?php
$pageTitle = 'Monitoring Jurusan';
ob_start();
?>

<div class="page-header">
    <h2><i class="fas fa-building"></i> Monitoring Jurusan – Kaprogli</h2>
    <p class="text-muted">Pantau progress pengisian nilai seluruh kelas dalam jurusan Anda.</p>
</div>

<!-- Pilih Jurusan -->
<div class="card mb-4">
    <div class="card-header">
        <h3><i class="fas fa-filter"></i> Pilih Jurusan</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="" class="filter-form">
            <input type="hidden" name="page" value="monitoring_kaprogli">
            <div class="form-row">
                <div class="form-group flex-2">
                    <label class="form-label">Jurusan</label>
                    <select name="jurusan_id" class="form-control" required>
                        <option value="">— Pilih Jurusan —</option>
                        <?php foreach ($jurusan_list as $j): ?>
                            <option value="<?= $j['id'] ?>" <?= $selected_jurusan_id == $j['id'] ? 'selected' : '' ?>>
                                <?= e($j['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group form-group-btn">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($jurusan && !empty($kelas_progress)): ?>

    <!-- Statistik Jurusan -->
    <div class="stats-grid mb-4">
        <div class="stat-card stat-blue">
            <div class="stat-icon"><i class="fas fa-chalkboard"></i></div>
            <div class="stat-body">
                <div class="stat-number">
                    <?= e($stats['total_kelas'] ?? 0) ?>
                </div>
                <div class="stat-label">Total Kelas</div>
            </div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="stat-body">
                <div class="stat-number">
                    <?= e($stats['total_guru'] ?? 0) ?>
                </div>
                <div class="stat-label">Total Guru</div>
            </div>
        </div>
        <div class="stat-card stat-orange">
            <div class="stat-icon"><i class="fas fa-book"></i></div>
            <div class="stat-body">
                <div class="stat-number">
                    <?= e($stats['total_pengampuan'] ?? 0) ?>
                </div>
                <div class="stat-label">Total Pengampuan</div>
            </div>
        </div>
    </div>

    <!-- Progress Per Kelas -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list-alt"></i> Progress Per Kelas – Jurusan
                <?= e($jurusan['nama']) ?>
            </h3>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kelas</th>
                        <th>Tahun Ajaran</th>
                        <th>Siswa</th>
                        <th>Mapel Lengkap</th>
                        <th>Progress</th>
                        <th>Status Kelas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kelas_progress as $i => $kp):
                        $totalM = (int) $kp['total_mapel'];
                        $lengkap = (int) ($kp['nilai_lengkap'] ?? 0);
                        $kPct = $totalM > 0 ? round(($lengkap / $totalM) * 100) : 0;
                        $rowClass = '';
                        if ($kp['status'] === 'final')
                            $rowClass = 'row-success';
                        elseif ($kPct < 50)
                            $rowClass = 'row-danger';
                        elseif ($kPct < 100)
                            $rowClass = 'row-warning';
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td>
                                <?= $i + 1 ?>
                            </td>
                            <td><strong>
                                    <?= e($kp['nama']) ?>
                                </strong></td>
                            <td>
                                <?= e($kp['tahun_ajaran']) ?>
                            </td>
                            <td>
                                <?= e($kp['total_siswa']) ?>
                            </td>
                            <td>
                                <span class="<?= $lengkap >= $totalM && $totalM > 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $lengkap ?> /
                                    <?= $totalM ?>
                                </span>
                            </td>
                            <td>
                                <div class="progress-cell">
                                    <div class="progress-bar-mini">
                                        <div class="progress-bar-fill <?= $kPct == 100 ? 'bg-green' : ($kPct < 50 ? 'bg-red' : '') ?>"
                                            style="width:<?= $kPct ?>%"></div>
                                    </div>
                                    <span>
                                        <?= $kPct ?>%
                                    </span>
                                </div>
                            </td>
                            <td>
                                <?= statusKelasBadge($kp['status']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Legend -->
    <div class="legend-bar">
        <span class="legend-item"><span class="dot dot-green"></span> Selesai / Final</span>
        <span class="legend-item"><span class="dot dot-yellow"></span> Sebagian lengkap</span>
        <span class="legend-item"><span class="dot dot-red"></span> Perlu perhatian (&lt;50%)</span>
    </div>

<?php elseif ($selected_jurusan_id > 0): ?>
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <p>Tidak ada data kelas untuk jurusan ini.</p>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>