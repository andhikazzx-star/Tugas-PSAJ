<?php
$pageTitle = 'Manajemen Kelas';
ob_start();
?>

<div class="page-header-actions">
    <div>
        <h2><i class="fas fa-chalkboard"></i> Manajemen Kelas</h2>
        <p class="text-muted">Kelola data kelas, wali kelas, dan tahun ajaran.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalCreateKelas')">
        <i class="fas fa-plus"></i> Tambah Kelas
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Kelas</th>
                    <th>Tingkat</th>
                    <th>Jurusan</th>
                    <th>Wali Kelas</th>
                    <th>Tahun Ajaran</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kelas_list as $i => $k): ?>
                    <tr>
                        <td>
                            <?= $i + 1 ?>
                        </td>
                        <td><strong>
                                <?= e($k['nama']) ?>
                            </strong></td>
                        <td>
                            <?= e($k['tingkat']) ?>
                        </td>
                        <td>
                            <?= e($k['jurusan_nama']) ?>
                        </td>
                        <td>
                            <?= e($k['wali_nama'] ?: '-') ?>
                        </td>
                        <td>
                            <?= e($k['tahun_ajaran_nama']) ?>
                        </td>
                        <td>
                            <?= statusKelasBadge($k['status']) ?>
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="?page=admin.siswa&kelas_id=<?= $k['id'] ?>" class="btn btn-sm btn-outline-info"
                                    title="Lihat Siswa">
                                    <i class="fas fa-users"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick="openEditKelas(<?= $k['id'] ?>, '<?= e(addslashes($k['nama'])) ?>', <?= $k['jurusan_id'] ?>, <?= $k['tingkat'] ?>, <?= $k['tahun_ajaran_id'] ?>, <?= (int) ($k['wali_id'] ?? 0) ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="?page=admin.kelas.delete" style="display:inline"
                                    onsubmit="return confirm('Hapus kelas <?= e(addslashes($k['nama'])) ?>?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="kelas_id" value="<?= $k['id'] ?>">
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
<div class="modal-overlay" id="modalCreateKelas">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Tambah Kelas Baru</h3>
            <button onclick="closeModal('modalCreateKelas')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="?page=admin.kelas.create">
            <?= csrfField() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Kelas * (Contoh: X RPL 1)</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama kelas..." required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tingkat *</label>
                        <select name="tingkat" class="form-control" required>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jurusan *</label>
                        <select name="jurusan_id" class="form-control" required>
                            <?php foreach ($jurusan_list as $j): ?>
                                <option value="<?= $j['id'] ?>">
                                    <?= e($j['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Tahun Ajaran *</label>
                    <select name="tahun_ajaran_id" class="form-control" required>
                        <?php foreach ($tahun_ajaran_list as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= $t['is_active'] ? 'selected' : '' ?>>
                                <?= e($t['nama']) ?>
                                <?= $t['is_active'] ? '(Aktif)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Wali Kelas</label>
                    <select name="wali_id" class="form-control">
                        <option value="0">-- Pilih Wali Kelas --</option>
                        <?php foreach ($wali_list as $w): ?>
                            <option value="<?= $w['id'] ?>">
                                <?= e($w['name']) ?>
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
<div class="modal-overlay" id="modalEditKelas">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Kelas</h3>
            <button onclick="closeModal('modalEditKelas')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="?page=admin.kelas.update">
            <?= csrfField() ?>
            <input type="hidden" name="kelas_id" id="editKelasId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Kelas *</label>
                    <input type="text" name="nama" id="editKelasNama" class="form-control" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tingkat *</label>
                        <select name="tingkat" id="editKelasTingkat" class="form-control" required>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jurusan *</label>
                        <select name="jurusan_id" id="editKelasJurusan" class="form-control" required>
                            <?php foreach ($jurusan_list as $j): ?>
                                <option value="<?= $j['id'] ?>">
                                    <?= e($j['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Tahun Ajaran *</label>
                    <select name="tahun_ajaran_id" id="editKelasTA" class="form-control" required>
                        <?php foreach ($tahun_ajaran_list as $t): ?>
                            <option value="<?= $t['id'] ?>">
                                <?= e($t['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Wali Kelas</label>
                    <select name="wali_id" id="editKelasWali" class="form-control">
                        <option value="0">-- Pilih Wali Kelas --</option>
                        <?php foreach ($wali_list as $w): ?>
                            <option value="<?= $w['id'] ?>">
                                <?= e($w['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditKelas(id, nama, jurusanId, tingkat, taId, waliId) {
        document.getElementById('editKelasId').value = id;
        document.getElementById('editKelasNama').value = nama;
        document.getElementById('editKelasJurusan').value = jurusanId;
        document.getElementById('editKelasTingkat').value = tingkat;
        document.getElementById('editKelasTA').value = taId;
        document.getElementById('editKelasWali').value = waliId;
        openModal('modalEditKelas');
    }
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>