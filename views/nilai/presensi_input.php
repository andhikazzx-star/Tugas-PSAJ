<?php
$pageTitle = 'Input Presensi Harian';
ob_start();
?>

<div class="page-header-actions">
    <div>
        <h2><i class="fas fa-user-check"></i> Input Presensi Harian</h2>
        <p class="text-muted"><?= e($pengampuan['mapel_nama']) ?> - <?= e($pengampuan['kelas_nama']) ?></p>
    </div>
</div>

<form method="POST" action="?page=presensi.save">
    <?= csrfField() ?>
    <input type="hidden" name="pengampuan_id" value="<?= $pengampuan['id'] ?>">
    <input type="hidden" name="semester" value="<?= $semester ?>">

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body bg-light">
            <div class="form-group mb-0" style="max-width: 300px;">
                <label class="form-label font-weight-bold">Tanggal Pertemuan</label>
                <input type="date" name="date" class="form-control" value="<?= $date ?>" required>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Siswa</th>
                        <th class="text-center" style="width: 350px;">Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($attendance)): ?>
                        <?php foreach ($attendance as $i => $s): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <strong><?= e($s['siswa_nama']) ?></strong><br>
                                    <small class="text-muted"><?= e($s['nis']) ?></small>
                                </td>
                                <td>
                                    <div class="attendance-options" style="display: flex; gap: 10px; justify-content: center;">
                                        <label class="radio-card">
                                            <input type="radio" name="presensi[<?= $s['siswa_id'] ?>]" value="H" <?= $s['status'] === 'H' || is_null($s['status']) ? 'checked' : '' ?>>
                                            <span class="radio-label bg-h">Hadir</span>
                                        </label>
                                        <label class="radio-card">
                                            <input type="radio" name="presensi[<?= $s['siswa_id'] ?>]" value="S" <?= $s['status'] === 'S' ? 'checked' : '' ?>>
                                            <span class="radio-label bg-s">Sakit</span>
                                        </label>
                                        <label class="radio-card">
                                            <input type="radio" name="presensi[<?= $s['siswa_id'] ?>]" value="I" <?= $s['status'] === 'I' ? 'checked' : '' ?>>
                                            <span class="radio-label bg-i">Izin</span>
                                        </label>
                                        <label class="radio-card">
                                            <input type="radio" name="presensi[<?= $s['siswa_id'] ?>]" value="A" <?= $s['status'] === 'A' ? 'checked' : '' ?>>
                                            <span class="radio-label bg-a">Alfa</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="notif-empty">Belum ada data siswa.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white p-3 d-flex justify-content-between">
            <a href="?page=presensi&pengampuan_id=<?= $pengampuan['id'] ?>&semester=<?= $semester ?>" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary shadow-sm">
                <i class="fas fa-save"></i> Simpan Presensi Harian
            </button>
        </div>
    </div>
</form>

<style>
    .radio-card input {
        display: none;
    }
    .radio-label {
        display: inline-block;
        padding: 8px 15px;
        border-radius: 5px;
        border: 1px solid #ddd;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s;
        min-width: 60px;
        text-align: center;
        background: #fff;
    }
    .radio-card input:checked + .bg-h { background: #2ecc71; color: #fff; border-color: #27ae60; }
    .radio-card input:checked + .bg-s { background: #f39c12; color: #fff; border-color: #d35400; }
    .radio-card input:checked + .bg-i { background: #3498db; color: #fff; border-color: #2980b9; }
    .radio-card input:checked + .bg-a { background: #e74c3c; color: #fff; border-color: #c0392b; }
    
    .radio-card:hover .radio-label {
        border-color: #999;
    }
</style>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>
