<?php
$pageTitle = 'Riwayat Presensi';
ob_start();
?>

<div class="page-header-actions">
    <div>
        <h2><i class="fas fa-calendar-alt"></i> Presensi: <?= e($pengampuan['mapel_nama']) ?></h2>
        <p class="text-muted">Kelas: <?= e($pengampuan['kelas_nama']) ?> | Semester <?= $semester ?></p>
    </div>
    <a href="?page=presensi.input&pengampuan_id=<?= $pengampuan['id'] ?>&semester=<?= $semester ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Input Presensi Baru
    </a>
</div>

<div class="card mb-4 shadow-sm">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kehadiran Siswa</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($dates)): ?>
                    <?php foreach ($dates as $i => $d): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= formatDate($d['tanggal']) ?></strong></td>
                            <td><?= $d['total_siswa'] ?> Siswa tercatat</td>
                            <td class="text-center">
                                <a href="?page=presensi.input&pengampuan_id=<?= $pengampuan['id'] ?>&semester=<?= $semester ?>&date=<?= $d['tanggal'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="notif-empty">Belum ada riwayat presensi. Silakan klik "Input Presensi Baru".</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="form-actions" style="justify-content: flex-start;">
    <a href="?page=nilai&pengampuan_id=<?= $pengampuan['id'] ?>&semester=<?= $semester ?>" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali ke Nilai
    </a>
</div>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>
