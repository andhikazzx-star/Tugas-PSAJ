<?php
$pageTitle = 'Input Nilai Ekskul';
ob_start();
?>

<div class="dashboard-grid">
    <section class="section-full">
        <div class="section-header">
            <div>
                <h2><i class="fas fa-edit"></i> Input Nilai: <?= htmlspecialchars($ekskul['nama']) ?></h2>
                <p class="text-muted">
                    Semester: <strong><?= $semester ?> (<?= $semester == 1 ? 'Ganjil' : 'Genap' ?>)</strong> | 
                    Tahun Ajaran: <strong><?= $tahun_ajaran ?></strong>
                </p>
            </div>
            <a href="?page=admin.ekskul" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="?page=admin.ekskul.save" method="POST">
            <input type="hidden" name="ekskul_id" value="<?= $ekskul['id'] ?>">
            <input type="hidden" name="semester" value="<?= $semester ?>">
            <input type="hidden" name="tahun_ajaran" value="<?= $tahun_ajaran ?>">
            
            <div class="card">
                <div class="table-responsive">
                    <table class="data-table">
                    <thead>
                        <tr>
                            <th>Siswa / Kelas</th>
                            <th style="width: 200px;">Nilai (A/B/C/D)</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswa_nilai as $s): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($s['siswa_nama']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($s['kelas_nama']) ?></small>
                                </td>
                                <td>
                                    <select name="nilai_ekskul[<?= $s['siswa_id'] ?>][nilai]" class="form-control">
                                        <option value="">- Pilih -</option>
                                        <option value="A" <?= ($s['nilai'] ?? '') == 'A' ? 'selected' : '' ?>>A (Sangat Baik)</option>
                                        <option value="B" <?= ($s['nilai'] ?? '') == 'B' ? 'selected' : '' ?>>B (Baik)</option>
                                        <option value="C" <?= ($s['nilai'] ?? '') == 'C' ? 'selected' : '' ?>>C (Cukup)</option>
                                        <option value="D" <?= ($s['nilai'] ?? '') == 'D' ? 'selected' : '' ?>>D (Kurang)</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="nilai_ekskul[<?= $s['siswa_id'] ?>][keterangan]" class="form-control" rows="1" placeholder="Misal: Aktif dan disiplin dalam latihan..."><?= htmlspecialchars($s['keterangan'] ?? '') ?></textarea>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>
                </div>
            </div>
            
            <div class="form-actions" style="margin-top: 2rem; text-align: right;">
                <button type="submit" class="btn btn-lg btn-success">
                    <i class="fas fa-save"></i> Simpan Nilai Ekstrakurikuler
                </button>
            </div>
        </form>
    </section>
</div>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>
