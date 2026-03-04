<?php
$pageTitle = 'Input Nilai';
ob_start();
?>

<div class="page-header-actions">
    <div>
        <h2><i class="fas fa-edit"></i> Input Nilai Mata Pelajaran</h2>
        <p class="text-muted">Masukkan nilai pengetahuan, keterampilan, dan kehadiran siswa.</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="form-row">
            <input type="hidden" name="page" value="nilai">
            <div class="form-group flex-2">
                <label class="form-label">Pilih Pengampuan *</label>
                <select name="pengampuan_id" class="form-control" onchange="this.form.submit()">
                    <option value="0">-- Pilih Mata Pelajaran & Kelas --</option>
                    <?php foreach ($pengampuan_list as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $selected_id === (int) $p['id'] ? 'selected' : '' ?>>
                            <?= e($p['mapel_nama']) ?> -
                            <?= e($p['kelas_nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Semester *</label>
                <select name="semester" class="form-control" onchange="this.form.submit()">
                    <option value="1" <?= $semester === 1 ? 'selected' : '' ?>>Ganjil (1)</option>
                    <option value="2" <?= $semester === 2 ? 'selected' : '' ?>>Genap (2)</option>
                </select>
            </div>
        </form>
    </div>
</div>

<?php if ($selected_id > 0 && $selected_pengampuan): ?>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white" style="background:var(--primary); color:#fff">
            <h3><i class="fas fa-list"></i> Daftar Siswa:
                <?= e($selected_pengampuan['mapel_nama']) ?> -
                <?= e($selected_pengampuan['kelas_nama']) ?>
            </h3>
        </div>
        <form method="POST" action="?page=nilai.save">
            <?= csrfField() ?>
            <input type="hidden" name="pengampuan_id" value="<?= $selected_id ?>">
            <input type="hidden" name="semester" value="<?= $semester ?>">

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="vertical-align:middle">No</th>
                            <th rowspan="2" style="vertical-align:middle">Nama Siswa</th>
                            <th colspan="2" class="text-center" style="text-align:center">Nilai Rata-rata</th>
                            <th colspan="3" class="text-center" style="text-align:center">Kehadiran (Per Mapel)</th>
                        </tr>
                        <tr>
                            <th style="width:120px">Pengetahuan</th>
                            <th style="width:120px">Keterampilan</th>
                            <th style="width:70px">Sakit</th>
                            <th style="width:70px">Izin</th>
                            <th style="width:70px">Alfa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($siswaNilai)): ?>
                            <?php foreach ($siswaNilai as $i => $s): ?>
                                <tr>
                                    <td>
                                        <?= $i + 1 ?>
                                    </td>
                                    <td>
                                        <strong>
                                            <?= e($s['siswa_nama']) ?>
                                        </strong><br>
                                        <small class="text-muted">NIS:
                                            <?= e($s['nis']) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <input type="number" name="nilai[<?= $s['siswa_id'] ?>][pengetahuan]"
                                            class="form-control input-sm" min="0" max="100" step="0.01"
                                            value="<?= e($s['pengetahuan']) ?>">
                                    </td>
                                    <td>
                                        <input type="number" name="nilai[<?= $s['siswa_id'] ?>][keterampilan]"
                                            class="form-control input-sm" min="0" max="100" step="0.01"
                                            value="<?= e($s['keterampilan']) ?>">
                                    </td>
                                    <td>
                                        <input type="number" name="nilai[<?= $s['siswa_id'] ?>][sakit]"
                                            class="form-control input-sm" min="0" value="<?= (int) $s['sakit'] ?>">
                                    </td>
                                    <td>
                                        <input type="number" name="nilai[<?= $s['siswa_id'] ?>][izin]" class="form-control input-sm"
                                            min="0" value="<?= (int) $s['izin'] ?>">
                                    </td>
                                    <td>
                                        <input type="number" name="nilai[<?= $s['siswa_id'] ?>][alfa]" class="form-control input-sm"
                                            min="0" value="<?= (int) $s['alfa'] ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="notif-empty">Belum ada data siswa di kelas ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-actions" style="justify-content: flex-end; padding: 1.5rem">
                <button type="submit" class="btn btn-lg btn-primary">
                    <i class="fas fa-save"></i> Simpan Seluruh Nilai
                </button>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-hand-pointer fa-3x mb-3 text-muted"></i>
        <h3>Silakan Pilih Pengampuan</h3>
        <p>Pilih mata pelajaran dan kelas yang ingin Anda input nilainya di atas.</p>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>