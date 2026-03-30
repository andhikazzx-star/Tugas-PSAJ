<?php

class AdminController
{
    private TahunAjaranModel $tahunAjaranModel;
    private KelasModel $kelasModel;
    private PengampuanModel $pengampuanModel;

    public function __construct()
    {
        $this->tahunAjaranModel = new TahunAjaranModel();
        $this->kelasModel = new KelasModel();
        $this->pengampuanModel = new PengampuanModel();
    }

    // ======================== TAHUN AJARAN BARU ========================

    /**
     * Halaman proses kenaikan kelas / tahun ajaran baru
     */
    public function tahunAjaranBaru(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        $activeYear = $this->tahunAjaranModel->getActive();

        // Hitung nama tahun berikutnya secara otomatis
        $nextYearName = '';
        if ($activeYear) {
            $parts = explode('/', $activeYear['nama']);
            if (count($parts) === 2) {
                $nextYearName = ((int)$parts[0] + 1) . '/' . ((int)$parts[1] + 1);
            }
        }

        renderView('admin/tahun_ajaran_baru', [
            'title' => 'Proses Tahun Ajaran Baru – ' . APP_NAME,
            'activeYear' => $activeYear,
            'nextYearName' => $nextYearName,
            'notFinalCount' => $this->kelasModel->countNotFinal()
        ]);
    }

    /**
     * Logika eksekusi tahun ajaran baru (Kenaikan Kelas)
     */
    public function processTahunAjaranBaru(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.tahun_ajaran_baru');

        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.tahun_ajaran_baru');
        }

        $confirmation = post('confirmation');
        if ($confirmation !== 'PROSES TAHUN AJARAN') {
            flashError('Konfirmasi tidak sesuai. Silakan ketik PROSES TAHUN AJARAN.');
            redirect('?page=admin.tahun_ajaran_baru');
        }

        $nextYearName = post('next_year_name');
        if (empty($nextYearName)) {
            flashError('Nama tahun ajaran baru tidak boleh kosong.');
            redirect('?page=admin.tahun_ajaran_baru');
        }

        if ($this->kelasModel->countNotFinal() > 0) {
            flashError('Proses dibatalkan. Masih ada kelas yang belum final.');
            redirect('?page=admin.tahun_ajaran_baru');
        }

        $pdo = getDB();
        try {
            $pdo->beginTransaction();

            // Get the current active year before deactivating
            $activeYear = $this->tahunAjaranModel->getActive();
            if (!$activeYear) {
                throw new Exception("Tahun ajaran aktif tidak ditemukan.");
            }
            $oldYearId = (int)$activeYear['id'];

            // 1. Deactivate all years
            $this->tahunAjaranModel->deactivateAll();

            // 2. Create and activate the new year
            $newYearId = $this->tahunAjaranModel->create($nextYearName, true);

            // 3. Process Promotion
            // Get all classes in the old year
            $stmt = $pdo->prepare("SELECT * FROM kelas WHERE tahun_ajaran_id = ?");
            $stmt->execute([$oldYearId]);
            $oldClasses = $stmt->fetchAll();

            foreach ($oldClasses as $k) {
                $oldTingkat = (int) $k['tingkat'];
                $oldNama = $k['nama'];
                $oldKelasId = (int) $k['id'];
                $jurusanId = (int) $k['jurusan_id'];

                if ($oldTingkat < 12) {
                    // Promote to next level
                    $newTingkat = $oldTingkat + 1;
                    $newNama = $oldNama;
                    
                    if ($oldTingkat == 10) {
                        $newNama = preg_replace('/^X\b/i', 'XI', $oldNama);
                    } elseif ($oldTingkat == 11) {
                        $newNama = preg_replace('/^XI\b/i', 'XII', $oldNama);
                    }

                    // Create new class for the new year
                    $newKelasId = $this->kelasModel->create($newNama, $jurusanId, $newTingkat, $newYearId);

                    // Move "aktif" students to the new class
                    $stmtMove = $pdo->prepare("UPDATE siswa SET kelas_id = ? WHERE kelas_id = ? AND status = 'aktif'");
                    $stmtMove->execute([$newKelasId, $oldKelasId]);
                    
                } else {
                    // Class 12 - Graduates
                    // Mark students as "lulus"
                    $stmtLulus = $pdo->prepare("UPDATE siswa SET status = 'lulus' WHERE kelas_id = ? AND status = 'aktif'");
                    $stmtLulus->execute([$oldKelasId]);

                    // Optionally create a new Class 10 record for incoming freshman replacing this graduation slot
                    $newNama = preg_replace('/^XII\b/i', 'X', $oldNama);
                    $this->kelasModel->create($newNama, $jurusanId, 10, $newYearId);
                }
            }

            // 4. Reset Assignment/Pengampuan for the new year (already empty since they were per class_id and year)
            // But we should delete old mapping if they were global? They are linked to kelas_id.
            $this->pengampuanModel->deleteAll(); 

            $pdo->commit();
            flashSuccess("Berhasil! Tahun ajaran baru ({$nextYearName}) telah aktif. Siswa telah dipromosikan dan yang lulus telah diarsipkan.");
            redirect('?page=dashboard');

        } catch (Exception $e) {
            $pdo->rollBack();
            flashError('Terjadi kesalahan saat promosi: ' . $e->getMessage());
            redirect('?page=admin.tahun_ajaran_baru');
        }
    }

    /**
     * Reset Data (Hanya untuk Mode Uji Coba)
     */
    public function resetTahunAjaran(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.tahun_ajaran_baru');

        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.tahun_ajaran_baru');
        }

        $pdo = getDB();
        try {
            $pdo->beginTransaction();

            $targetYearName = '2026/2027';
            $stmtActive = $pdo->query("SELECT id FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
            $activeYear = $stmtActive->fetch();

            if ($activeYear) {
                $stmtExist = $pdo->prepare("SELECT id FROM tahun_ajaran WHERE nama = ? AND id != ? LIMIT 1");
                $stmtExist->execute([$targetYearName, $activeYear['id']]);
                $otherYear = $stmtExist->fetch();

                if ($otherYear) {
                    $pdo->query("UPDATE tahun_ajaran SET is_active = 0");
                    $stmtActivate = $pdo->prepare("UPDATE tahun_ajaran SET is_active = 1 WHERE id = ?");
                    $stmtActivate->execute([$otherYear['id']]);
                } else {
                    $stmtUpdateYear = $pdo->prepare("UPDATE tahun_ajaran SET nama = ? WHERE id = ?");
                    $stmtUpdateYear->execute([$targetYearName, $activeYear['id']]);
                }
            }

            // Hapus data transaksional
            $pdo->query("DELETE FROM nilai");
            $pdo->query("DELETE FROM absensi_mapel");
            $pdo->query("DELETE FROM catatan_wali");
            $pdo->query("DELETE FROM ekskul_nilai");
            $pdo->query("UPDATE kelas SET status = 'proses'");

            $pdo->commit();
            flashSuccess("Berhasil reset data ke {$targetYearName}.");
        } catch (Exception $e) {
            $pdo->rollBack();
            flashError('Gagal reset: ' . $e->getMessage());
        }

        redirect('?page=admin.tahun_ajaran_baru');
    }
}
