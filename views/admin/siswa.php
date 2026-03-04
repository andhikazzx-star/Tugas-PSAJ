<?php
$pageTitle = 'Manajemen Siswa';
ob_start();
?>

<div class="page-header-actions">
    <div>
        <h2><i class="fas fa-user-graduate"></i> Manajemen Siswa</h2>
        <p class="text-muted">Kelola data siswa di seluruh kelas.</p>
    </div>
    <div class="action-btns">
        <button class="btn btn-outline-primary" onclick="openModal('modalImportSiswa')">
            <i class="fas fa-file-import"></i> Import Excel
        </button>
        <button class="btn btn-primary" onclick="openModal('modalCreateSiswa')">
            <i class="fas fa-plus"></i> Tambah Siswa
        </button>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="form-row">
            <input type="hidden" name="page" value="admin.siswa">
            <div class="form-group flex-2">
                <label class="form-label"><i class="fas fa-filter"></i> Filter Kelas</label>
                <select name="kelas_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua Kelas --</option>
                    <?php foreach ($kelas_list as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= (int) get_param('kelas_id') === (int) $k['id'] ? 'selected' : '' ?>>
                            <?= e($k['nama']) ?> (<?= e($k['jurusan_nama']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group flex-2">
                <label class="form-label"><i class="fas fa-search"></i> Cari Nama/NIS</label>
                <div class="input-icon-right">
                    <input type="text" name="q" class="form-control" placeholder="Cari..."
                        value="<?= e(get_param('q')) ?>">
                    <button type="submit" class="input-icon-btn"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Lengkap</th>
                    <th>NIS</th>
                    <th>NISN</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>Status</th>
                    <th>Aksi</th>
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
                            <?= e($s['nis'] ?: '-') ?>
                        </td>
                        <td>
                            <?= e($s['nisn'] ?: '-') ?>
                        </td>
                        <td>
                            <?= e($s['kelas_nama']) ?>
                        </td>
                        <td>
                            <?= e($s['jurusan_nama']) ?>
                        </td>
                        <td>
                            <span class="badge <?= $s['status'] === 'aktif' ? 'badge-success' : 'badge-secondary' ?>">
                                <?= ucfirst(e($s['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-btns">
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick="openEditSiswa(<?= $s['id'] ?>, '<?= e(addslashes($s['nama'])) ?>', '<?= e($s['nis']) ?>', '<?= e($s['nisn']) ?>', <?= $s['kelas_id'] ?>, '<?= $s['status'] ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="?page=admin.siswa.delete" style="display:inline"
                                    onsubmit="return confirm('Hapus siswa <?= e(addslashes($s['nama'])) ?>?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="siswa_id" value="<?= $s['id'] ?>">
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Create -->
<div class="modal-overlay" id="modalCreateSiswa">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Tambah Siswa Baru</h3>
            <button onclick="closeModal('modalCreateSiswa')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="?page=admin.siswa.create">
            <?= csrfField() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama lengkap..." required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">NIS</label>
                        <input type="text" name="nis" class="form-control" placeholder="NIS...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">NISN</label>
                        <input type="text" name="nisn" class="form-control" placeholder="NISN...">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Kelas *</label>
                    <select name="kelas_id" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelas_list as $k): ?>
                            <option value="<?= $k['id'] ?>">
                                <?= e($k['nama']) ?> (
                                <?= e($k['jurusan_nama']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal-overlay" id="modalEditSiswa">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Data Siswa</h3>
            <button onclick="closeModal('modalEditSiswa')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="?page=admin.siswa.update">
            <?= csrfField() ?>
            <input type="hidden" name="siswa_id" id="editSiswaId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="nama" id="editSiswaNama" class="form-control" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">NIS</label>
                        <input type="text" name="nis" id="editSiswaNis" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">NISN</label>
                        <input type="text" name="nisn" id="editSiswaNisn" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kelas *</label>
                        <select name="kelas_id" id="editSiswaKelas" class="form-control" required>
                            <?php foreach ($kelas_list as $k): ?>
                                <option value="<?= $k['id'] ?>">
                                    <?= e($k['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status *</label>
                        <select name="status" id="editSiswaStatus" class="form-control" required>
                            <option value="aktif">Aktif</option>
                            <option value="lulus">Lulus</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditSiswa(id, nama, nis, nisn, kelasId, status) {
        document.getElementById('editSiswaId').value = id;
        document.getElementById('editSiswaNama').value = nama;
        document.getElementById('editSiswaNis').value = nis;
        document.getElementById('editSiswaNisn').value = nisn;
        document.getElementById('editSiswaKelas').value = kelasId;
        document.getElementById('editSiswaStatus').value = status;
        openModal('modalEditSiswa');
    }
</script>

<div class="modal-overlay" id="modalImportSiswa">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-file-import"></i> Import Siswa (Bulk)</h3>
            <button onclick="closeModal('modalImportSiswa')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="?page=admin.siswa.import">
            <?= csrfField() ?>
            <div class="modal-body">
                <div class="alert alert-info" style="font-size: 0.8rem;">
                    <i class="fas fa-info-circle"></i> 
                    <div>
                        Format: <strong>Nama;NIS;NISN</strong> (per baris)<br>
                        Pemisah bisa menggunakan titik koma (;), koma (,), atau Tab (Copy-paste dari Excel).
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Pilih Kelas Tujuan *</label>
                    <select name="kelas_id" class="form-control" required autosave-id="import_kelas_id">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelas_list as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= (int)get_param('kelas_id') === (int)$k['id'] ? 'selected' : '' ?>>
                                <?= e($k['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Data Siswa (Paste dari Excel/Text)</label>
                    <textarea name="csv_data" class="form-control" rows="10" 
                        placeholder="Contoh:&#10;Andi Saputra;12345;0012345678&#10;Budi Cahyono;12346;0012345679"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Mulai Import</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>