<?php
$pageTitle = 'Input Nilai & Presensi';
ob_start();
?>

<div class="page-header-actions">
    <div>
        <h2><i class="fas fa-edit"></i> Input Nilai & Presensi Mapel</h2>
        <p class="text-muted">Kelola nilai sumatif, PTS, dan absensi kehadiran per mata pelajaran.</p>
    </div>
    <div class="header-tools">
        <span class="badge badge-soft-success">Tahun Ajaran Aktif</span>
    </div>
</div>

<!-- Pemilihan Mapel -->
<div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; border-left: 5px solid var(--primary) !important;">
    <div class="card-body p-4">
        <form method="GET" class="form-row align-items-end">
            <input type="hidden" name="page" value="nilai">
            <div class="col-md-5">
                <label class="form-label font-weight-bold">Pengampuan Saya</label>
                <select name="pengampuan_id" class="form-control" onchange="this.form.submit()" style="border-radius: 8px;">
                    <option value="0">-- Pilih Mata Pelajaran & Kelas --</option>
                    <?php foreach ($pengampuan_list as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $selected_id === (int) $p['id'] ? 'selected' : '' ?>>
                            <?= e($p['mapel_nama']) ?> | <?= e($p['kelas_nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label font-weight-bold">Semester</label>
                <select name="semester" class="form-control" onchange="this.form.submit()" style="border-radius: 8px;">
                    <option value="1" <?= $semester === 1 ? 'selected' : '' ?>>1 (Ganjil)</option>
                    <option value="2" <?= $semester === 2 ? 'selected' : '' ?>>2 (Genap)</option>
                </select>
            </div>
            <div class="col-md-4">
                <?php if ($selected_id > 0): ?>
                    <a href="?page=presensi&pengampuan_id=<?= $selected_id ?>&semester=<?= $semester ?>" class="btn btn-outline-primary btn-block" style="border-radius: 8px; border-style: dashed;">
                        <i class="fas fa-calendar-alt"></i> Detail Presensi Harian
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php if ($selected_id > 0 && $selected_pengampuan): ?>
    <form method="POST" action="?page=nilai.save">
        <?= csrfField() ?>
        <input type="hidden" name="pengampuan_id" value="<?= $selected_id ?>">
        <input type="hidden" name="semester" value="<?= $semester ?>">

        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 text-primary">
                    <i class="fas fa-users mr-2"></i> 
                    <?= e($selected_pengampuan['mapel_nama']) ?> — <?= e($selected_pengampuan['kelas_nama']) ?>
                </h4>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="vertical-align: middle;">
                    <thead class="bg-light text-center text-uppercase small font-weight-bold">
                        <tr>
                            <th rowspan="2" class="align-middle" style="width: 50px; border-top:none;">No</th>
                            <th rowspan="2" class="align-middle text-left" style="border-top:none;">Nama Lengkap</th>
                            <th colspan="4" class="py-2" style="background-color: rgba(52, 152, 219, 0.05); border-top:none; border-bottom: 2px solid #3498db;">Nilai Capaian (Harian & PTS)</th>
                            <th colspan="3" class="py-2" style="background-color: rgba(46, 204, 113, 0.05); border-top:none; border-bottom: 2px solid #2ecc71;">Absensi (Kehadiran)</th>
                        </tr>
                        <tr>
                            <th style="width: 85px; font-size: 0.75rem;">Sum I</th>
                            <th style="width: 85px; font-size: 0.75rem;">Sum II</th>
                            <th style="width: 85px; font-size: 0.75rem;">Sum III</th>
                            <th style="width: 85px; font-size: 0.75rem; color: #e67e22;">PTS</th>
                            <th style="width: 70px; font-size: 0.75rem;">S</th>
                            <th style="width: 70px; font-size: 0.75rem;">I</th>
                            <th style="width: 70px; font-size: 0.75rem;">A</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($siswaNilai)): ?>
                            <?php foreach ($siswaNilai as $i => $s): ?>
                                <tr>
                                    <td class="text-center text-muted" data-label="No"><?= $i + 1 ?></td>
                                    <td data-label="Siswa">
                                        <div style="font-weight: 600; font-size: 0.95rem;"><?= e($s['siswa_nama']) ?></div>
                                        <span class="text-muted small"><?= e($s['nis']) ?></span>
                                    </td>
                                    
                                    <!-- Nilai Fields -->
                                    <td class="p-1" data-label="SUM I">
                                        <input type="number" name="nilai[<?= $s['siswa_id'] ?>][s1]" 
                                            class="form-control form-control-sm text-center border-0 bg-light-hover" 
                                            value="<?= $s['s1'] ?: '' ?>" placeholder="-" step="0.01" min="0" max="100">
                                    </td>
                                    <td class="p-1" data-label="SUM II">
                                        <input type="number" name="nilai[<?= $s['siswa_id'] ?>][s2]" 
                                            class="form-control form-control-sm text-center border-0 bg-light-hover" 
                                            value="<?= $s['s2'] ?: '' ?>" placeholder="-" step="0.01" min="0" max="100">
                                    </td>
                                    <td class="p-1" data-label="SUM III">
                                        <input type="number" name="nilai[<?= $s['siswa_id'] ?>][s3]" 
                                            class="form-control form-control-sm text-center border-0 bg-light-hover" 
                                            value="<?= $s['s3'] ?: '' ?>" placeholder="-" step="0.01" min="0" max="100">
                                    </td>
                                    <td class="p-1" data-label="PTS">
                                        <input type="number" name="nilai[<?= $s['siswa_id'] ?>][pts]" 
                                            class="form-control form-control-sm text-center border-0 font-weight-bold" 
                                            value="<?= $s['pts'] ?: '' ?>" placeholder="-" step="0.01" min="0" max="100"
                                            style="background-color: rgba(230, 126, 34, 0.05); color: #d35400;">
                                    </td>

                                    <!-- Absensi Fields -->
                                    <td class="p-1" data-label="SAKIT">
                                        <input type="number" name="nilai[<?= $s['siswa_id'] ?>][sakit]" 
                                            class="form-control form-control-sm text-center border-0 no-spinners" 
                                            value="<?= (int) $s['sakit'] ?>" min="0">
                                    </td>
                                    <td class="p-1" data-label="IZIN">
                                        <input type="number" name="nilai[<?= $s['siswa_id'] ?>][izin]" 
                                            class="form-control form-control-sm text-center border-0 no-spinners" 
                                            value="<?= (int) $s['izin'] ?>" min="0">
                                    </td>
                                    <td class="p-1" data-label="ALFA">
                                        <input type="number" name="nilai[<?= $s['siswa_id'] ?>][alfa]" 
                                            class="form-control form-control-sm text-center border-0 no-spinners" 
                                            value="<?= (int) $s['alfa'] ?>" min="0">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-user-slash fa-3x mb-3"></i>
                                        <p>Belum ada siswa yang terdaftar di kelas ini.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-light p-4 d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    <i class="fas fa-lightbulb text-warning mr-1"></i> Data disimpan secara batch. Tekan tombol simpan di kanan bawah.
                </div>
                <button type="submit" class="btn btn-primary shadow" style="border-radius: 8px; padding: 0.6rem 2.5rem; font-weight: 600;">
                    <i class="fas fa-save mr-2"></i> SIMPAN SEMUA NILAI
                </button>
            </div>
        </div>
    </form>

    <style>
        .table td { border-top: 1px solid #f2f2f2; }
        .bg-light-hover:hover { background-color: #e9ecef !important; }
        .form-control:focus { background-color: #fff; box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25); border-color: #3498db; }
        .no-spinners::-webkit-outer-spin-button, .no-spinners::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .no-spinners { -moz-appearance: textfield; }

        @media (max-width: 991.98px) {
            .table-responsive { overflow: visible !important; }
            .table thead { display: none; }
            .table tbody tr { 
                display: block; 
                margin-bottom: 2rem; 
                border: 1px solid #e0e0e0; 
                border-radius: 15px; 
                padding: 15px; 
                box-shadow: 0 5px 15px rgba(0,0,0,0.05);
                background: #fff;
            }
            .table td { 
                display: flex; 
                justify-content: space-between; 
                align-items: center; 
                border: none !important; 
                padding: 10px 5px !important; 
                border-bottom: 1px dashed #f0f0f0 !important;
            }
            .table td:last-child { border-bottom: none !important; }
            .table td::before { 
                content: attr(data-label); 
                font-weight: 800; 
                font-size: 0.7rem; 
                color: #888;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .table td[data-label="No"] { 
                background: #f8f9fa; 
                margin: -15px -15px 10px -15px;
                border-radius: 12px 12px 0 0; 
                justify-content: center;
                font-weight: bold;
                padding: 12px !important;
                border-bottom: 1px solid #eee !important;
            }
            .table td[data-label="Siswa"] { 
                display: block;
                text-align: center;
                border-bottom: 2px solid var(--primary) !important; 
                margin-bottom: 15px;
                padding-bottom: 15px !important;
            }
            .table td[data-label="Siswa"]::before { display: none; }
            
            .form-control-sm { 
                max-width: 140px; 
                font-size: 1rem !important; 
                height: 40px !important;
                background-color: #f9f9f9 !important;
                border: 1px solid #ddd !important;
            }
            .card-header h4 { font-size: 1.1rem; }
            .page-header-actions h2 { font-size: 1.4rem; }
        }
    </style>

<?php else: ?>
    <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 12px;">
        <div class="card-body">
            <h3 class="text-muted">Pilih Mata Pelajaran & Kelas</h3>
            <p class="text-muted mb-0">Silakan pilih pengampuan Anda untuk mulai menginput nilai dan absensi.</p>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/main.php';
?>