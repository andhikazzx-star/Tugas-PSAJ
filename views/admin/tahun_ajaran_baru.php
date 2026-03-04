<?php
$pageTitle = 'Manajemen Tahun Ajaran';
ob_start();
?>

<div class="page-header-actions">
    <div>
        <h2><i class="fas fa-calendar-alt"></i> Pusat Manajemen Tahun Ajaran</h2>
        <p class="text-muted">Kelola data akademik, promosi kelas, dan pembersihan data uji coba.</p>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Current Status Header -->
    <section class="section-full">
        <div class="status-banner"
            style="display: flex; gap: 1rem; align-items: center; background: #fff; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 2rem;">
            <div class="status-item"
                style="flex: 1; display: flex; align-items: center; gap: 1.2rem; border-right: 1px solid #eee;">
                <div
                    style="width: 48px; height: 48px; background: #eef2ff; color: #4361ee; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 1.4rem;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <span style="display: block; font-size: 0.85rem; color: #718096; font-weight: 500;">Tahun Ajaran
                        Aktif</span>
                    <strong style="font-size: 1.25rem; color: #1a202c;"><?= e($activeYear['nama'] ?? '-') ?></strong>
                </div>
            </div>

            <div class="status-item" style="flex: 1; display: flex; align-items: center; gap: 1.2rem;">
                <div
                    style="width: 48px; height: 48px; background: <?= $notFinalCount > 0 ? '#fff7ed' : '#f0fdf4' ?>; color: <?= $notFinalCount > 0 ? '#c2410c' : '#15803d' ?>; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 1.4rem;">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div>
                    <span style="display: block; font-size: 0.85rem; color: #718096; font-weight: 500;">Status
                        Monitoring</span>
                    <strong style="font-size: 1.25rem; color: #1a202c;"><?= e($notFinalCount) ?> Kelas Belum
                        Final</strong>
                </div>
            </div>
        </div>
    </section>

    <!-- Action Areas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">

        <!-- Action 1: New Year Promotion -->
        <div class="card"
            style="border: none; border-radius: 15px; overflow: hidden; background: #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
            <div style="padding: 1.5rem; background: #4361ee; color: white;">
                <h3 style="display: flex; align-items: center; gap: 0.8rem; margin: 0; font-weight: 600;">
                    <i class="fas fa-arrow-up"></i> Promosi Tahun Ajaran Baru
                </h3>
            </div>
            <div style="padding: 2rem;">
                <p style="color: #4a5568; line-height: 1.6;">Proses ini akan menaikkan tingkat seluruh siswa, menutup
                    tahun ajaran saat ini, dan membuka lembaran baru.</p>

                <div
                    style="background: #f8fafc; border: 1px dashed #cbd5e0; padding: 1.2rem; border-radius: 10px; margin-bottom: 2rem;">
                    <div style="display: flex; gap: 0.8rem; margin-bottom: 0.8rem;">
                        <i class="fas fa-check-circle" style="color: #4361ee; margin-top: 3px;"></i>
                        <span style="font-size: 0.9rem; color: #4a5568;">Siswa Kelas X &raquo; XI, XI &raquo; XII</span>
                    </div>
                    <div style="display: flex; gap: 0.8rem; margin-bottom: 0.8rem;">
                        <i class="fas fa-check-circle" style="color: #4361ee; margin-top: 3px;"></i>
                        <span style="font-size: 0.9rem; color: #4a5568;">Siswa Kelas XII &raquo; <strong
                                style="color: #1a202c;">LULUS</strong></span>
                    </div>
                    <div style="display: flex; gap: 0.8rem;">
                        <i class="fas fa-check-circle" style="color: #4361ee; margin-top: 3px;"></i>
                        <span style="font-size: 0.9rem; color: #4a5568;">Reset Penugasan Guru (Pengampuan)</span>
                    </div>
                </div>

                <?php if ($notFinalCount > 0): ?>
                    <div class="lock-overlay"
                        style="padding: 1.2rem; background: #fff5f5; border: 1px solid #feb2b2; border-radius: 12px; text-align: center;">
                        <i class="fas fa-lock"
                            style="color: #c53030; font-size: 1.5rem; margin-bottom: 0.5rem; display: block;"></i>
                        <p style="color: #c53030; font-weight: 600; margin-bottom: 0.3rem;">Tombol Terkunci</p>
                        <p style="font-size: 0.85rem; color: #c53030;">Harap finalisasi monitoring seluruh kelas terlebih
                            dahulu sebelum dapat melakukan promosi.</p>
                    </div>
                    <button class="btn btn-secondary" disabled
                        style="width: 100%; padding: 1rem; border-radius: 10px; margin-top: 1rem; cursor: not-allowed; opacity: 0.6;">
                        Promosi Belum Tersedia
                    </button>
                <?php else: ?>
                    <form method="POST" action="?page=admin.tahun_ajaran_baru.process">
                        <?= csrfField() ?>
                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label style="font-weight: 600; color: #4a5568; display: block; margin-bottom: 0.5rem;">Ganti ke
                                Tahun Ajaran:</label>
                            <input type="text" name="next_year_name" class="form-control" value="<?= e($nextYearName) ?>"
                                style="border: 2px solid #e2e8f0; border-radius: 10px; padding: 0.8rem;" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 2rem;">
                            <label
                                style="font-weight: 600; color: #4a5568; display: block; margin-bottom: 0.5rem;">Konfirmasi
                                Keamanan:</label>
                            <p style="font-size: 0.8rem; color: #718096; margin-bottom: 0.8rem;">Ketik: <code
                                    style="background: #edf2f7; padding: 2px 6px; border-radius: 4px; color: #4361ee; font-weight: 700;">PROSES TAHUN AJARAN</code>
                            </p>
                            <input type="text" name="confirmation" class="form-control" placeholder="..."
                                style="border: 2px solid #e2e8f0; border-radius: 10px; padding: 0.8rem;" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-action-hover"
                            style="width: 100%; padding: 1rem; border-radius: 10px; font-weight: 700; background: #4361ee; border: none; color: white; cursor: pointer;">
                            <i class="fas fa-paper-plane" style="margin-right: 8px;"></i> Jalankan Promosi Masal
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action 2: Reset Trial (Back to 2026/2027) -->
        <div class="card"
            style="border: none; border-radius: 15px; overflow: hidden; background: #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
            <div style="padding: 1.5rem; background: #e53e3e; color: white;">
                <h3 style="display: flex; align-items: center; gap: 0.8rem; margin: 0; font-weight: 600;">
                    <i class="fas fa-undo-alt"></i> Reset Data (Mode Uji Coba)
                </h3>
            </div>
            <div style="padding: 2rem;">
                <p style="color: #4a5568; line-height: 1.6;">Fitur khusus uji coba untuk membersihkan data nilai tanpa
                    mengganti kelas siswa. Tahun ajaran akan dikembalikan ke asal.</p>

                <div
                    style="background: #fff5f5; border: 1px dashed #feb2b2; padding: 1.2rem; border-radius: 10px; margin-bottom: 2rem;">
                    <div style="display: flex; gap: 0.8rem; margin-bottom: 1rem;">
                        <i class="fas fa-redo-alt" style="color: #e53e3e; margin-top: 3px;"></i>
                        <span style="font-size: 0.9rem; color: #c53030;">Tahun Ajaran Aktif &rarr; <strong
                                style="color: #000;">2026/2027</strong></span>
                    </div>
                    <div style="display: flex; gap: 0.8rem; margin-bottom: 1rem;">
                        <i class="fas fa-eraser" style="color: #e53e3e; margin-top: 3px;"></i>
                        <span style="font-size: 0.9rem; color: #c53030;">Hapus Seluruh Data Nilai & Kehadiran</span>
                    </div>
                    <div style="display: flex; gap: 0.8rem;">
                        <i class="fas fa-user-friends" style="color: #e53e3e; margin-top: 3px;"></i>
                        <span style="font-size: 0.9rem; color: #c53030;">Data Siswa & Kelas Tetap Utuh</span>
                    </div>
                </div>

                <form method="POST" action="?page=admin.tahun_ajaran_baru.reset"
                    onsubmit="return confirm('PERHATIAN! Tindakan ini akan menghapus semua nilai dan mengatur ulang tahun ke 2026/2027. Lanjutkan?')">
                    <?= csrfField() ?>
                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label style="font-weight: 600; color: #4a5568; display: block; margin-bottom: 0.5rem;">Simbol
                            Keamanan:</label>
                        <p style="font-size: 0.8rem; color: #718096; margin-bottom: 0.8rem;">Ketik kalimat konfirmasi di
                            bawah:</p>
                        <input type="text" name="confirmation" class="form-control"
                            placeholder="RESET DATA TAHUN AJARAN"
                            style="border: 2px solid #e2e8f0; border-radius: 10px; padding: 0.8rem;" required>
                    </div>
                    <button type="submit" class="btn btn-danger btn-action-hover"
                        style="width: 100%; padding: 1rem; border-radius: 10px; font-weight: 700; background: #e53e3e; border: none; color: white; cursor: pointer;">
                        <i class="fas fa-sync-alt" style="margin-right: 8px;"></i> Bersihkan & Kembali ke 2026/2027
                    </button>
                    <p style="text-align: center; font-size: 0.8rem; color: #718096; margin-top: 1.2rem;">
                        <i class="fas fa-info-circle"></i> Memungkinkan Anda melakukan simulasi input nilai kembali dari
                        nol.
                    </p>
                </form>
            </div>
        </div>

    </div>
</div>

<style>
    .btn-action-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-action-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        filter: brightness(1.1);
    }

    .status-banner {
        transition: transform 0.3s ease;
    }

    .status-banner:hover {
        transform: scale(1.005);
    }

    .form-control:focus {
        border-color: #4361ee !important;
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1) !important;
        outline: none;
    }

    @media (max-width: 768px) {
        .status-banner {
            flex-direction: column;
            text-align: center;
        }

        .status-item {
            border-right: none !important;
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
            justify-content: center !important;
        }
    }
</style>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>