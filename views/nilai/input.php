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
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="background:var(--primary); color:#fff; display: flex; justify-content: space-between; align-items: center;">
            <h3 class="mb-0"><i class="fas fa-list"></i> Daftar Siswa:
                <?= e($selected_pengampuan['mapel_nama']) ?> -
                <?= e($selected_pengampuan['kelas_nama']) ?>
            </h3>
            <a href="?page=presensi&pengampuan_id=<?= $selected_id ?>&semester=<?= $semester ?>" class="btn btn-sm btn-light" style="background:#fff; color:var(--primary); font-weight:bold;">
                <i class="fas fa-calendar-check"></i> Menu Presensi Harian (Detail)
            </a>
        </div>
        <form method="POST" action="?page=nilai.save">
            <?= csrfField() ?>
            <input type="hidden" name="pengampuan_id" value="<?= $selected_id ?>">
            <input type="hidden" name="semester" value="<?= $semester ?>">

            <div class="table-responsive">
                <table class="data-table table-hover">
                    <thead>
                        <tr class="bg-light">
                            <th rowspan="2" style="vertical-align:middle; width: 50px;">No</th>
                            <th rowspan="2" style="vertical-align:middle;">Nama Siswa</th>
                            <th colspan="2" class="text-center bg-grade" style="text-align:center; background: rgba(52, 152, 219, 0.1);">
                                <i class="fas fa-graduation-cap"></i> Nilai Capaian
                            </th>
                            <th colspan="3" class="text-center bg-attendance" style="text-align:center; background: rgba(46, 204, 113, 0.1);">
                                <i class="fas fa-user-check"></i> Ketidakhadiran (S/I/A)
                            </th>
                        </tr>
                        <tr>
                            <th style="width:130px; text-align:center;">Pengetahuan</th>
                            <th style="width:130px; text-align:center;">Keterampilan</th>
                            <th style="width:80px; text-align:center; color: #f39c12;">Sakit</th>
                            <th style="width:80px; text-align:center; color: #3498db;">Izin</th>
                            <th style="width:80px; text-align:center; color: #e74c3c;">Alfa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($siswaNilai)): ?>
                            <?php foreach ($siswaNilai as $i => $s): ?>
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td>
                                        <div class="siswa-info">
                                            <span class="siswa-nama"><?= e($s['siswa_nama']) ?></span>
                                            <code class="siswa-nis"><?= e($s['nis']) ?></code>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group-sm">
                                            <input type="number" name="nilai[<?= $s['siswa_id'] ?>][pengetahuan]"
                                                class="form-control text-center input-nilai" min="0" max="100" step="0.01"
                                                value="<?= e($s['pengetahuan']) ?>" placeholder="0.00">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group-sm">
                                            <input type="number" name="nilai[<?= $s['siswa_id'] ?>][keterampilan]"
                                                class="form-control text-center input-nilai" min="0" max="100" step="0.01"
                                                value="<?= e($s['keterampilan']) ?>" placeholder="0.00">
                                        </div>
                                    </td>
                                    <td style="background: rgba(243, 156, 18, 0.05);">
                                        <input type="number" name="nilai[<?= $s['siswa_id'] ?>][sakit]"
                                            class="form-control text-center no-spinners" min="0" 
                                            value="<?= (int) $s['sakit'] ?>" style="border-color: #f39c12;">
                                    </td>
                                    <td style="background: rgba(52, 152, 219, 0.05);">
                                        <input type="number" name="nilai[<?= $s['siswa_id'] ?>][izin]" 
                                            class="form-control text-center no-spinners" min="0" 
                                            value="<?= (int) $s['izin'] ?>" style="border-color: #3498db;">
                                    </td>
                                    <td style="background: rgba(231, 76, 60, 0.05);">
                                        <input type="number" name="nilai[<?= $s['siswa_id'] ?>][alfa]" 
                                            class="form-control text-center no-spinners" min="0" 
                                            value="<?= (int) $s['alfa'] ?>" style="border-color: #e74c3c;">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="notif-empty">
                                    <div class="p-5 text-center">
                                        <i class="fas fa-users-slash fa-3x mb-3 text-muted"></i>
                                        <p>Belum ada data siswa di kelas ini.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-actions" style="justify-content: space-between; align-items: center; padding: 2rem; border-top: 1px solid #eee; background: #fafafa;">
                <div class="text-muted small">
                    <i class="fas fa-info-circle"></i> Tip: Gunakan tombol <strong>TAB</strong> untuk berpindah antar kolom dengan cepat.
                </div>
                <button type="submit" class="btn btn-lg btn-primary shadow-sm" style="padding-left: 3rem; padding-right: 3rem;">
                    <i class="fas fa-save"></i> SIMPAN SEMUA DATA
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