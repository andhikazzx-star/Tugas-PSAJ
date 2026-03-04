<?php
$pageTitle = 'Manajemen Mata Pelajaran';
ob_start();
?>

<div class="page-header-actions">
    <div>
        <h2><i class="fas fa-book"></i> Manajemen Mata Pelajaran</h2>
        <p class="text-muted">Kelola mata pelajaran per jurusan.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalCreate')">
        <i class="fas fa-plus"></i> Tambah Mapel
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Mapel</th>
                    <th>Jurusan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mapel_list as $i => $m): ?>
                    <tr>
                        <td>
                            <?= $i + 1 ?>
                        </td>
                        <td><strong>
                                <?= e($m['nama']) ?>
                            </strong></td>
                        <td>
                            <?= e($m['jurusan_nama']) ?>
                        </td>
                        <td>
                            <div class="action-btns">
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick="openEdit(<?= $m['id'] ?>, '<?= e(addslashes($m['nama'])) ?>', <?= $m['jurusan_id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="?page=admin.mapel.delete" style="display:inline"
                                    onsubmit="return confirm('Hapus mapel <?= e(addslashes($m['nama'])) ?>?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="mapel_id" value="<?= $m['id'] ?>">
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

<!-- Modal Tambah -->
<div class="modal-overlay" id="modalCreate">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle"></i> Tambah Mata Pelajaran</h3>
            <button onclick="closeModal('modalCreate')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="?page=admin.mapel.create">
            <?= csrfField() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Mata Pelajaran *</label>
                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Matematika" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jurusan *</label>
                    <select name="jurusan_id" class="form-control" required>
                        <option value="">— Pilih Jurusan —</option>
                        <?php foreach ($jurusan_list as $j): ?>
                            <option value="<?= $j['id'] ?>">
                                <?= e($j['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modalCreate')" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal-overlay" id="modalEdit">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Mata Pelajaran</h3>
            <button onclick="closeModal('modalEdit')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="?page=admin.mapel.update">
            <?= csrfField() ?>
            <input type="hidden" name="mapel_id" id="editId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Mata Pelajaran *</label>
                    <input type="text" name="nama" id="editNama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jurusan *</label>
                    <select name="jurusan_id" id="editJurusan" class="form-control" required>
                        <option value="">— Pilih Jurusan —</option>
                        <?php foreach ($jurusan_list as $j): ?>
                            <option value="<?= $j['id'] ?>">
                                <?= e($j['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modalEdit')" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEdit(id, nama, jurusanId) {
        document.getElementById('editId').value = id;
        document.getElementById('editNama').value = nama;
        document.getElementById('editJurusan').value = jurusanId;
        openModal('modalEdit');
    }
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>