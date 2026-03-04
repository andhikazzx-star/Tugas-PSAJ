<?php
$pageTitle = 'Manajemen Pengguna';
ob_start();
?>

<div class="page-header-actions">
    <div>
        <h2><i class="fas fa-users"></i> Manajemen Pengguna</h2>
        <p class="text-muted">Kelola semua akun pengguna dan peran (multi-role).</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalCreateUser')">
        <i class="fas fa-plus"></i> Tambah Pengguna
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $i => $u): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= e($u['name']) ?></strong></td>
                        <td><?= e($u['nip'] ?: '-') ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td>
                            <?php foreach (explode(', ', $u['roles'] ?? '') as $r): ?>
                                <?php if (trim($r)): ?>
                                    <span
                                        class="role-badge role-<?= e(trim($r)) ?>"><?= e(ucfirst(str_replace('_', ' ', trim($r)))) ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <div class="action-btns">
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick="openEditUser(<?= $u['id'] ?>, '<?= e(addslashes($u['name'])) ?>', '<?= e($u['email']) ?>', '<?= e($u['nip']) ?>', '<?= e($u['roles'] ?? '') ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($u['id'] !== Session::getUserId()): ?>
                                    <form method="POST" action="?page=admin.users.delete" style="display:inline"
                                        onsubmit="return confirm('Hapus pengguna <?= e(addslashes($u['name'])) ?>?')">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modalCreateUser">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Tambah Pengguna Baru</h3>
            <button onclick="closeModal('modalCreateUser')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="?page=admin.users.create">
            <?= csrfField() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama..." required>
                </div>
                <div class="form-group">
                    <label class="form-label">NIP (Hanya untuk Guru)</label>
                    <input type="text" name="nip" class="form-control" placeholder="NIP jika ada...">
                </div>
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" placeholder="email@..." required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password * (min. 6 karakter)</label>
                    <input type="password" name="password" class="form-control" placeholder="Password..." required
                        minlength="6">
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <div class="checkbox-group">
                        <?php foreach ($roles as $r): ?>
                            <label class="checkbox-label">
                                <input type="checkbox" name="role_ids[]" value="<?= $r['id'] ?>">
                                <span><?= e(ucfirst(str_replace('_', ' ', $r['name']))) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modalEditUser">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-user-edit"></i> Edit Pengguna</h3>
            <button onclick="closeModal('modalEditUser')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="?page=admin.users.update">
            <?= csrfField() ?>
            <input type="hidden" name="user_id" id="editUserId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="name" id="editUserName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">NIP (Hanya untuk Guru)</label>
                    <input type="text" name="nip" id="editUserNip" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" id="editUserEmail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password Baru (kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="form-control" placeholder="Biarkan kosong"
                        minlength="6">
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <div class="checkbox-group" id="editRoleList">
                        <?php foreach ($roles as $r): ?>
                            <label class="checkbox-label">
                                <input type="checkbox" name="role_ids[]" value="<?= $r['id'] ?>" class="edit-role-cb"
                                    data-role="<?= e($r['name']) ?>">
                                <span><?= e(ucfirst(str_replace('_', ' ', $r['name']))) ?></span>
                            </label>
                        <?php endforeach; ?>
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
    function openEditUser(id, name, email, nip, roles) {
        document.getElementById('editUserId').value = id;
        document.getElementById('editUserName').value = name;
        document.getElementById('editUserEmail').value = email;
        document.getElementById('editUserNip').value = nip;
        const roleArr = roles.split(', ').map(r => r.trim());
        document.querySelectorAll('.edit-role-cb').forEach(cb => {
            cb.checked = roleArr.includes(cb.dataset.role);
        });
        openModal('modalEditUser');
    }
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>