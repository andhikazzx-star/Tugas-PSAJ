<?php

class NilaiModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getByMapelKelas(int $mapelId, int $kelasId, int $semester): array
    {
        // 1. Get raw attendance totals from daily records (optional feature)
        $stmtAbs = $this->db->prepare(
            "SELECT am.siswa_id, 
                    SUM(CASE WHEN am.status = 'S' THEN 1 ELSE 0 END) as s_harian,
                    SUM(CASE WHEN am.status = 'I' THEN 1 ELSE 0 END) as i_harian,
                    SUM(CASE WHEN am.status = 'A' THEN 1 ELSE 0 END) as a_harian
             FROM absensi_mapel am
             JOIN pengampuan p ON p.id = am.pengampuan_id
             WHERE p.mapel_id = ? AND p.kelas_id = ? AND am.semester = ?
             GROUP BY am.siswa_id"
        );
        $stmtAbs->execute([$mapelId, $kelasId, $semester]);
        $dailyTotals = $stmtAbs->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

        // 2. Get grades and final attendance (overrides)
        $stmt = $this->db->prepare(
            "SELECT s.id as siswa_id, s.nama as siswa_nama, s.nis,
                    n.id as nilai_id, n.pengetahuan as pts, n.keterampilan, 
                    n.s1, n.s2, n.s3, n.status,
                    k.sakit, k.izin, k.alfa
             FROM siswa s
             LEFT JOIN nilai n ON n.siswa_id = s.id AND n.mapel_id = ? AND n.semester = ?
             LEFT JOIN kehadiran k ON k.siswa_id = s.id AND k.mapel_id = ? AND k.semester = ?
             WHERE s.kelas_id = ? AND s.status = 'aktif'
             ORDER BY s.nama ASC"
        );
        $stmt->execute([$mapelId, $semester, $mapelId, $semester, $kelasId]);
        $rows = $stmt->fetchAll();

        // 3. Merge: If final attendance (k.sakit etc) is NULL, use daily totals
        foreach ($rows as &$row) {
            $sid = $row['siswa_id'];
            if ($row['sakit'] === null) $row['sakit'] = $dailyTotals[$sid]['s_harian'] ?? 0;
            if ($row['izin'] === null) $row['izin'] = $dailyTotals[$sid]['i_harian'] ?? 0;
            if ($row['alfa'] === null) $row['alfa'] = $dailyTotals[$sid]['a_harian'] ?? 0;
        }

        return $rows;
    }

    public function saveBatchNilai(int $mapelId, int $kelasId, int $semester, array $data): array
    {
        $taModel = new TahunAjaranModel();
        $activeYear = $taModel->getActive();
        $tahunAjaran = $activeYear ? $activeYear['nama'] : '';

        try {
            $this->db->beginTransaction();

            foreach ($data as $siswaId => $nilai) {
                // Save Nilai
                $stmt = $this->db->prepare(
                    "INSERT INTO nilai (siswa_id, mapel_id, semester, pengetahuan, s1, s2, s3, status)
                     VALUES (?, ?, ?, ?, ?, ?, ?, 'lengkap')
                     ON DUPLICATE KEY UPDATE 
                        pengetahuan = VALUES(pengetahuan), 
                        s1 = VALUES(s1),
                        s2 = VALUES(s2),
                        s3 = VALUES(s3),
                        status = 'lengkap'"
                );
                $stmt->execute([
                    $siswaId,
                    $mapelId,
                    $semester,
                    $nilai['pts'] !== '' ? $nilai['pts'] : null,
                    $nilai['s1'] !== '' ? $nilai['s1'] : 0,
                    $nilai['s2'] !== '' ? $nilai['s2'] : 0,
                    $nilai['s3'] !== '' ? $nilai['s3'] : 0
                ]);

                // Save Kehadiran
                $stmtK = $this->db->prepare(
                    "INSERT INTO kehadiran (siswa_id, mapel_id, semester, tahun_ajaran, sakit, izin, alfa)
                     VALUES (?, ?, ?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE 
                        sakit = VALUES(sakit),
                        izin = VALUES(izin),
                        alfa = VALUES(alfa)"
                );
                $stmtK->execute([
                    $siswaId,
                    $mapelId,
                    $semester,
                    $tahunAjaran,
                    (int) ($nilai['sakit'] ?? 0),
                    (int) ($nilai['izin'] ?? 0),
                    (int) ($nilai['alfa'] ?? 0)
                ]);
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'Nilai dan absensi berhasil disimpan.'];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Gagal menyimpan nilai: ' . $e->getMessage()];
        }
    }

    public function getMapelStatusDiKelas(int $kelasId, int $semester): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.mapel_id, m.nama as mapel_nama, m.kategori,
                    CASE 
                        WHEN COUNT(DISTINCT s.id) = SUM(CASE WHEN n.status = 'lengkap' THEN 1 ELSE 0 END) THEN 'lengkap'
                        ELSE 'belum'
                    END as status
             FROM pengampuan p
             JOIN mapel m ON m.id = p.mapel_id
             JOIN siswa s ON s.kelas_id = p.kelas_id AND s.status = 'aktif'
             LEFT JOIN nilai n ON n.siswa_id = s.id AND n.mapel_id = p.mapel_id AND n.semester = ?
             WHERE p.kelas_id = ? AND p.status = 'approved'
             GROUP BY p.mapel_id, m.nama, m.kategori
             ORDER BY m.kategori ASC, m.nama ASC"
        );
        $stmt->execute([$semester, $kelasId]);
        return $stmt->fetchAll();
    }

    public function getGradesBySiswa(int $siswaId, int $semester): array
    {
        $stmt = $this->db->prepare(
            "SELECT n.*, m.nama as mapel_nama, m.kategori, m.kktp as kkm,
                    k.sakit, k.izin, k.alfa
             FROM nilai n
             JOIN mapel m ON m.id = n.mapel_id
             LEFT JOIN kehadiran k ON k.siswa_id = n.siswa_id AND k.mapel_id = n.mapel_id AND k.semester = ?
             WHERE n.siswa_id = ? AND n.semester = ?
             ORDER BY m.kategori ASC, m.nama ASC"
        );
        $stmt->execute([$semester, $siswaId, $semester]);
        return $stmt->fetchAll();
    }

    public function getCatatanSiswa(int $siswaId, int $semester): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM catatan_wali WHERE siswa_id = ? AND semester = ? LIMIT 1"
        );
        $stmt->execute([$siswaId, $semester]);
        return $stmt->fetch() ?: ['sikap' => '', 'catatan' => ''];
    }

    public function saveCatatan(int $siswaId, int $semester, ?string $sikap, ?string $catatan): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO catatan_wali (siswa_id, semester, sikap, catatan)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE sikap = VALUES(sikap), catatan = VALUES(catatan)"
        );
        return $stmt->execute([$siswaId, $semester, $sikap, $catatan]);
    }

    public function getEkskulBySiswa(int $siswaId, int $semester): array
    {
        // Get active year for correct filtering
        $taModel = new TahunAjaranModel();
        $activeYear = $taModel->getActive();
        $tahunAjaran = $activeYear ? $activeYear['nama'] : '2024/2025';

        // Get ONLY from structured table (Pembina Ekskul)
        $stmt = $this->db->prepare(
            "SELECT m.nama as nama_kegiatan, 
                    CONCAT('Predikat: ', en.nilai, '. ', en.keterangan) as keterangan
             FROM ekskul_nilai en
             JOIN master_ekskul m ON m.id = en.ekskul_id
             WHERE en.siswa_id = ? AND en.semester = ? AND en.tahun_ajaran = ?"
        );
        $stmt->execute([$siswaId, $semester, $tahunAjaran]);
        return $stmt->fetchAll();
    }


}
