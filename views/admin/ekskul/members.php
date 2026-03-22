<?php
$pageTitle = 'Kelola Anggota Ekskul';
ob_start();
?>

<div class="dashboard-grid">
    <section class="section-full">
        <div class="section-header">
            <div>
                <h2><i class="fas fa-users"></i> Anggota: <?= htmlspecialchars($ekskul['nama']) ?></h2>
                <p class="text-muted">
                    Semester: <strong><?= $semester ?></strong> | Tahun Ajaran: <strong><?= $tahun_ajaran ?></strong>
                </p>
            </div>
            <a href="?page=admin.ekskul" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="row" style="display: flex; gap: 20px; flex-wrap: wrap;">
            <!-- Left: Current Members -->
            <div style="flex: 1; min-width: 300px;">
                <div class="card shadow-sm">
                    <div class="card-header" style="padding: 15px; border-bottom: 1px solid #eaeaea;">
                        <h4 class="mb-0" style="font-size: 1.1rem; color: #333;"><i class="fas fa-list text-primary"></i> Daftar Anggota Aktif</h4>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <?php if (empty($members)): ?>
                            <div class="p-4 text-center text-muted">Belum ada anggota yang ditambahkan.</div>
                        <?php else: ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa / Kelas</th>
                                        <th style="width: 100px; text-align:center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($members as $m): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($m['siswa_nama']) ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($m['kelas_nama']) ?></small>
                                            </td>
                                            <td style="text-align:center;">
                                                <a href="?page=admin.ekskul.remove_member&ekskul_id=<?= $ekskul['id'] ?>&siswa_id=<?= $m['siswa_id'] ?>&semester=<?= $semester ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Hapus siswa ini dari keanggotaan?')">
                                                   <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right: Search and Add -->
            <div style="flex: 1; min-width: 300px;">
                <div class="card shadow-sm">
                    <div class="card-header" style="padding: 15px; border-bottom: 1px solid #eaeaea;">
                        <h4 class="mb-0" style="font-size: 1.1rem; color: #333;"><i class="fas fa-user-plus text-success"></i> Pilih & Tambah Anggota</h4>
                    </div>
                    <div class="card-body" style="padding: 15px;">
                        <form action="" method="GET" class="search-form" style="display: flex; gap: 10px; margin-bottom: 20px;">
                            <input type="hidden" name="page" value="admin.ekskul.members">
                            <input type="hidden" name="ekskul_id" value="<?= $ekskul['id'] ?>">
                            <input type="hidden" name="semester" value="<?= $semester ?>">
                            <select name="kelas_id" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelas_list as $k): ?>
                                    <option value="<?= $k['id'] ?>" <?= (isset($kelas_filter) && $kelas_filter == $k['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($k['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Tampilkan</button>
                        </form>

                        <?php if (!empty($available_siswa)): ?>
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Daftar Siswa Kelas Ini</th>
                                            <th style="width: 100px; text-align:center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($available_siswa as $s): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($s['nama']) ?></strong><br>
                                                    <small class="text-muted"><?= htmlspecialchars($s['kelas_nama']) ?></small>
                                                </td>
                                                <td style="text-align:center;">
                                                    <a href="?page=admin.ekskul.add_member&ekskul_id=<?= $ekskul['id'] ?>&siswa_id=<?= $s['id'] ?>&semester=<?= $semester ?>" 
                                                       class="btn btn-sm btn-success">
                                                       <i class="fas fa-plus"></i> Tambah
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php elseif (isset($kelas_filter) && $kelas_filter > 0): ?>
                            <div class="text-center text-muted p-3">Tidak ada siswa di kelas ini atau semua siswa sudah lulus/tidak aktif.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>
