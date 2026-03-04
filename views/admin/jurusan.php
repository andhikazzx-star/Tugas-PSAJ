<?php
$pageTitle = 'Manajemen Jurusan';
ob_start();
?>

<div class="page-header-actions">
    <div>
        <h2><i class="fas fa-sitemap"></i> Manajemen Jurusan</h2>
        <p class="text-muted">Kelola jurusan dan mapping kaprogli.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalCreate')">
        <i class="fas fa-plus"></i> Tambah Jurusan
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Jurusan</th>
                    <th>Kaprogli</th>
                    <th>Total Kelas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jurusan_list as $i => $j): ?>
                    <tr>
                        <td>
                            <?= $i + 1 ?>
                        </td>
                        <td><strong>
                                <?= e($j['nama']) ?>
                            </strong></td>
                        <td>
                            <?= $j['kaprogli_nama'] ? e($j['kaprogli_nama']) : '<em class="text-muted">Belum ditentukan</em>' ?>
                        </td>
                        <td>
                            <?= $j['total_kelas'] ?> Kelas
                        </td>
                        <td>
                            <div class="action-btns">
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick="openEdit(<?= $j['id'] ?>, '<?= e(addslashes($j['nama'])) ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="?page=admin.jurusan.delete" style="display:inline"
                                    onsubmit="return confirm('Hapus jurusan <?= e(addslashes($j['nama'])) ?>?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="jurusan_id" value="<?= $j['id'] ?>">
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
            <h3><i class="fas fa-plus-circle"></i> Tambah Jurusan</h3>
            <button onclick="closeModal('modalCreate')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="?page=admin.jurusan.create">
            <?= csrfField() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Jurusan *</label>
                    <input type="text" name="nama" class="form-control"
                        placeholder="Contoh: Teknik Komputer dan Jaringan" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kaprogli</label>
                    <select name="kaprogli_id" class="form-control">
                        <option value="">— Pilih Kaprogli —</option>
                        <?php foreach ($kaprogli_list as $k): ?>
                            <option value="<?= $k['id'] ?>">
                                <?= e($k['name']) ?>
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
            <h3><i class="fas fa-edit"></i> Edit Jurusan</h3>
            <button onclick="closeModal('modalEdit')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="?page=admin.jurusan.update">
            <?= csrfField() ?>
            <input type="hidden" name="jurusan_id" id="editId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Jurusan *</label>
                    <input type="text" name="nama" id="editNama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kaprogli</label>
                    <select name="kaprogli_id" class="form-control">
                        <option value="">— Pilih Kaprogli —</option>
                        <?php foreach ($kaprogli_list as $k): ?>
                            <option value="<?= $k['id'] ?>">
                                <?= e($k['name']) ?>
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
    function openEdit(id, nama) {
        document.getElementById('editId').value = id;
        document.getElementById('editNama').value = nama;
        openModal('modalEdit');
    }
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>