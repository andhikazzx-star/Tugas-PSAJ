<?php
$pageTitle = 'Monitoring Nilai';
ob_start();
?>

<div class="page-header-actions">
    <div>
        <h2><i class="fas fa-chart-line"></i> Monitoring Progress Nilai</h2>
        <p class="text-muted">Pantau kelengkapan nilai dari seluruh guru mata pelajaran di kelas Anda.</p>
    </div>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" class="form-row">
            <input type="hidden" name="page" value="monitoring">
            <div class="form-group flex-2">
                <label class="form-label">Pilih Kelas Perwalian *</label>
                <select name="kelas_id" class="form-control" onchange="this.form.submit()">
                    <option value="0">-- Pilih Kelas --</option>
                    <?php foreach ($kelas_list as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $selected_kelas_id === (int) $k['id'] ? 'selected' : '' ?>>
                            <?= e($k['nama']) ?> -
                            <?= e($k['jurusan_nama']) ?>
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

<?php if ($selected_kelas_id > 0 && $kelas): ?>
    <div class="dashboard-grid">
        <section class="section-full">
            <div class="card">
                <div class="card-header bg-primary text-white"
                    style="display:flex; justify-content:space-between; align-items:center;">
                    <h3 style="color:#fff"><i class="fas fa-tasks"></i> Progress Guru Mata Pelajaran</h3>
                    <?php if ($kelas['status'] !== 'final'): ?>
                        <form method="POST" action="?page=monitoring.finalisasi"
                            onsubmit="return confirm('Apakah Anda yakin ingin melakukan finalisasi rapor untuk kelas ini? Data yang telah di-finalisasi tidak dapat diubah kembali.')">
                            <?= csrfField() ?>
                            <input type="hidden" name="kelas_id" value="<?= $selected_kelas_id ?>">
                            <input type="hidden" name="semester" value="<?= $semester ?>">
                            <button type="submit" class="btn btn-sm btn-success" <?= $total_belum_lengkap > 0 ? 'disabled title="Masih ada mapel belum lengkap"' : '' ?>>
                                <i class="fas fa-check-double"></i> Finalisasi Kelas
                            </button>
                        </form>
                    <?php else: ?>
                        <span class="badge badge-success"><i class="fas fa-lock"></i> KELAS TERFINALISASI</span>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Mata Pelajaran</th>
                                <th>Kategori</th>
                                <th>Status Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($mapel_status)): ?>
                                <?php foreach ($mapel_status as $i => $ms): ?>
                                    <tr>
                                        <td>
                                            <?= $i + 1 ?>
                                        </td>
                                        <td><strong>
                                                <?= e($ms['mapel_nama']) ?>
                                            </strong></td>
                                        <td>
                                            <?= e($ms['kategori']) ?>
                                        </td>
                                        <td>
                                            <?= statusNilaiBadge($ms['status']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="notif-empty">Belum ada data pengampuan di kelas ini.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Daftar Siswa dan Tombol Cetak / Input Catatan -->
        <section class="section-full mt-4">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-user-graduate"></i> Daftar Siswa & Rapor Sisipan</h3>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Email/User</th>
                                <th>Aksi Wali Kelas</th>
                                <th>Cetak Rapor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($siswa_list as $i => $s): ?>
                                <tr>
                                    <td>
                                        <?= $i + 1 ?>
                                    </td>
                                    <td><strong>
                                            <?= e($s['nama']) ?>
                                        </strong></td>
                                    <td>
                                        <?= e($s['nis']) ?>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="btn btn-sm btn-outline-primary"
                                                onclick="openCatatanModal(<?= $s['id'] ?>, '<?= e(addslashes($s['nama'])) ?>', '<?= e(addslashes($s['sikap'])) ?>', '<?= e(addslashes($s['catatan'])) ?>', <?= $semester ?>)">
                                                <i class="fas fa-comment-dots"></i> Catatan & Sikap
                                            </button>
                                            <button class="btn btn-sm btn-outline-info"
                                                onclick="openEkskulModal(<?= $s['id'] ?>, '<?= e(addslashes($s['nama'])) ?>', <?= $semester ?>)">
                                                <i class="fas fa-hiking"></i> Ekskul
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="?page=rapor.print&siswa_id=<?= $s['id'] ?>&semester=<?= $semester ?>"
                                                target="_blank" class="btn btn-sm btn-outline">
                                                <i class="fas fa-print"></i> Print (HTML)
                                            </a>
                                            <a href="?page=rapor.print&siswa_id=<?= $s['id'] ?>&semester=<?= $semester ?>&format=pdf"
                                                class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-chalkboard-teacher fa-3x mb-3 text-muted"></i>
        <h3>Pilih Kelas Perwalian</h3>
        <p>Gunakan form di atas untuk melihat progress nilai kelas yang Anda ampu sebagai wali kelas.</p>
    </div>
<?php endif; ?>

<!-- Modal Catatan -->
<div class="modal-overlay" id="modalCatatan">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="catatanTitle">Catatan Wali Kelas</h3>
            <button onclick="closeModal('modalCatatan')" class="modal-close">&times;</button>
        </div>
        <form id="formCatatan">
            <?= csrfField() ?>
            <input type="hidden" name="siswa_id" id="catSiswaId">
            <input type="hidden" name="semester" id="catSemester">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Perkembangan Sikap & Karakter</label>
                    <textarea name="sikap" id="catSikap" class="form-control" rows="3"
                        placeholder="Sikap spiritual & sosial..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan Wali Kelas</label>
                    <textarea name="catatan" id="catCatatan" class="form-control" rows="4"
                        placeholder="Catatan motivasi untuk siswa..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="btnSaveCatatan">Simpan Catatan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCatatanModal(id, nama, sikap, catatan, semester) {
        document.getElementById('catSiswaId').value = id;
        document.getElementById('catSemester').value = semester;
        document.getElementById('catSikap').value = sikap;
        document.getElementById('catCatatan').value = catatan;
        document.getElementById('catatanTitle').innerText = 'Catatan Wali Kelas: ' + nama;
        openModal('modalCatatan');
    }

    document.getElementById('formCatatan').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveCatatan');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        const formData = new FormData(this);
        fetch('?page=monitoring.save_catatan', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Gagal: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = 'Simpan Catatan';
                }
            });
    });

    function openEkskulModal(id, nama, semester) {
        // Simple implementation: redirect to a specific ekskul page or use modal with AJAX
        // For now, redirecting to save ekskul flow
        alert('Fitur manajemen Ekskul untuk ' + nama + ' sedang dimuat.');
        // Implementation for Ekskul would go here
    }
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>