<?php
$pageTitle = 'Manajemen Guru Mata Pelajaran';
ob_start();
?>

<div class="page-header-actions">
    <div>
        <h2><i class="fas fa-hands-helping"></i> Manajemen Guru Mapel</h2>
        <p class="text-muted">Tugaskan guru penganjar untuk setiap mata pelajaran di setiap kelas.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalCreatePengampuan')">
        <i class="fas fa-plus"></i> Tambah Pengampuan
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Guru</th>
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pengampuan_list as $i => $p): ?>
                    <tr>
                        <td>
                            <?= $i + 1 ?>
                        </td>
                        <td><strong>
                                <?= e($p['guru_nama']) ?>
                            </strong></td>
                        <td>
                            <?= e($p['mapel_nama']) ?>
                        </td>
                        <td>
                            <?= e($p['kelas_nama']) ?>
                        </td>
                        <td>
                            <div class="action-btns">
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick="openEditPengampuan(<?= $p['id'] ?>, <?= $p['guru_id'] ?>, <?= $p['mapel_id'] ?>, <?= $p['kelas_id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="?page=admin.pengampuan.delete" style="display:inline"
                                    onsubmit="return confirm('Hapus pengampuan ini?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="pengampuan_id" value="<?= $p['id'] ?>">
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
<div class="modal-overlay" id="modalCreatePengampuan">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Tambah Pengampuan Baru</h3>
            <button onclick="closeModal('modalCreatePengampuan')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="?page=admin.pengampuan.create">
            <?= csrfField() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Guru Pengajar *</label>
                    <select name="guru_id" class="form-control" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach ($guru_list as $g): ?>
                            <option value="<?= $g['id'] ?>">
                                <?= e($g['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Mata Pelajaran *</label>
                        <select name="mapel_id" class="form-control" required>
                            <option value="">-- Pilih Mapel --</option>
                            <?php foreach ($mapel_list as $m): ?>
                                <option value="<?= $m['id'] ?>">
                                    <?= e($m['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kelas *</label>
                        <select name="kelas_id" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas_list as $k): ?>
                                <option value="<?= $k['id'] ?>">
                                    <?= e($k['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal-overlay" id="modalEditPengampuan">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Pengampuan</h3>
            <button onclick="closeModal('modalEditPengampuan')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="?page=admin.pengampuan.update">
            <?= csrfField() ?>
            <input type="hidden" name="pengampuan_id" id="editPengampuanId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Guru Pengajar *</label>
                    <select name="guru_id" id="editGuruId" class="form-control" required>
                        <?php foreach ($guru_list as $g): ?>
                            <option value="<?= $g['id'] ?>">
                                <?= e($g['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Mata Pelajaran *</label>
                        <select name="mapel_id" id="editMapelId" class="form-control" required>
                            <?php foreach ($mapel_list as $m): ?>
                                <option value="<?= $m['id'] ?>">
                                    <?= e($m['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kelas *</label>
                        <select name="kelas_id" id="editKelasId" class="form-control" required>
                            <?php foreach ($kelas_list as $k): ?>
                                <option value="<?= $k['id'] ?>">
                                    <?= e($k['nama']) ?>
                                </option>
                            <?php endforeach; ?>
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
    function openEditPengampuan(id, guruId, mapelId, kelasId) {
        document.getElementById('editPengampuanId').value = id;
        document.getElementById('editGuruId').value = guruId;
        document.getElementById('editMapelId').value = mapelId;
        document.getElementById('editKelasId').value = kelasId;
        openModal('modalEditPengampuan');
    }
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>