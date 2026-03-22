<?php
$pageTitle = 'Manajemen Ekskul';
ob_start();
?>

<div class="page-header-actions">
    <div>
        <h2><i class="fas fa-volleyball-ball"></i> Manajemen Ekstrakurikuler</h2>
        <p class="text-muted">Kelola daftar ekskul dan tunjuk pembina untuk masing-masing ekskul.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalCreateEkskul')">
        <i class="fas fa-plus"></i> Tambah Ekskul
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Ekstrakurikuler</th>
                    <th>Pembina</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ekskul_list as $i => $e): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= e($e['nama']) ?></strong></td>
                        <td>
                            <?php if (!empty($e['pembina_nama'])): ?>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: #dcfce7; color: #166534; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px;">
                                        <?= strtoupper(substr(trim($e['pembina_nama']), 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #1f2937; line-height: 1.2;"><?= e($e['pembina_nama']) ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem; margin-top: 2px;">
                                            <i class="fas fa-envelope" style="font-size: 0.7rem;"></i> 
                                            <?= strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $e['pembina_nama'])) ?>@gmail.com
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="badge" style="background:#fef3c7; color:#166534; font-weight:500; padding:6px 10px; border-radius:6px;">
                                    <i class="fas fa-exclamation-circle text-warning"></i> Belum ada pembina
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px; align-items: center; justify-content: flex-start; flex-wrap: wrap;">
                                <button class="btn btn-sm btn-success" title="Edit Ekskul"
                                    onclick="openEditEkskul(<?= $e['id'] ?>, '<?= e(addslashes($e['nama'])) ?>', '<?= e(addslashes($e['pembina_nama'] ?? '')) ?>')">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <form method="POST" action="?page=admin.ekskul.delete" style="display:inline; margin:0;"
                                    onsubmit="return confirm('Hapus ekskul <?= e(addslashes($e['nama'])) ?>?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="ekskul_id" value="<?= $e['id'] ?>">
                                    <button class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($ekskul_list)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-4">Belum ada data ekskul.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Create -->
<div class="modal-overlay" id="modalCreateEkskul">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Tambah Ekskul Baru</h3>
            <button onclick="closeModal('modalCreateEkskul')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="?page=admin.ekskul.create">
            <?= csrfField() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Ekstrakurikuler *</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama ekskul..." required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Pembina</label>
                    <input type="text" name="pembina_nama" class="form-control" placeholder="Nama lengkap pembina (opsional)">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal-overlay" id="modalEditEkskul">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Ekskul</h3>
            <button onclick="closeModal('modalEditEkskul')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="?page=admin.ekskul.update">
            <?= csrfField() ?>
            <input type="hidden" name="ekskul_id" id="editEkskulId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Ekstrakurikuler *</label>
                    <input type="text" name="nama" id="editEkskulNama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Pembina</label>
                    <input type="text" name="pembina_nama" id="editEkskulPembina" class="form-control" placeholder="Nama lengkap pembina (opsional)">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditEkskul(id, nama, pembinaNama) {
        document.getElementById('editEkskulId').value = id;
        document.getElementById('editEkskulNama').value = nama;
        document.getElementById('editEkskulPembina').value = pembinaNama;
        openModal('modalEditEkskul');
    }
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>
