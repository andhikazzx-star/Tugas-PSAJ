<?php
$pageTitle = 'Input Anggota Ekskul';
ob_start();
?>

<div class="dashboard-grid">
    <section class="section-full">
        <div class="section-header">
            <h2><i class="fas fa-users"></i> Input Anggota Ekskul</h2>
            <span class="badge badge-warning">Pembina Ekskul</span>
        </div>

        <?php if (empty($ekskul_list)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>Anda belum ditugaskan mengampu ekstrakurikuler apapun.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Ekstrakurikuler</th>
                            <th style="width: 200px;">Aksi (Input Anggota)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ekskul_list as $e): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($e['nama']) ?></strong>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px; align-items: center; justify-content: flex-start; flex-wrap: wrap;">
                                        <div class="btn-group">
                                            <a href="?page=ekskul.members&ekskul_id=<?= $e['id'] ?>&semester=1" class="btn btn-sm btn-success" title="Anggota S1"><i class="fas fa-users"></i> S1</a>
                                            <a href="?page=ekskul.members&ekskul_id=<?= $e['id'] ?>&semester=2" class="btn btn-sm btn-success" title="Anggota S2"><i class="fas fa-users"></i> S2</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>
