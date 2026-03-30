<?php

class NilaiModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /**
     * Guru: Get students and their existing grades + attendance for input form
     * Also merges daily attendance totals if manual override isn't present
     */
    public function getByMapelKelas(int $mapelId, int $kelasId, int $semester): array
    {
        $taModel = new TahunAjaranModel();
        $activeYear = $taModel->getActive();
        $taId = $activeYear ? (int)$activeYear['id'] : 0;

        // 1. Get raw attendance totals from daily records (absensi_mapel table)
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

        // 2. Get existing grades and final attendance from unified 'nilai' table
        $stmt = $this->db->prepare(
            "SELECT s.id as siswa_id, s.nama as siswa_nama, s.nis,
                    n.id as nilai_id, n.pts, n.s1, n.s2, n.s3, n.status,
                    n.sakit, n.izin, n.alfa
             FROM siswa s
             LEFT JOIN nilai n ON n.siswa_id = s.id 
                AND n.mapel_id = ? 
                AND n.semester = ? 
                AND n.tahun_ajaran_id = ?
             WHERE s.kelas_id = ? AND s.status = 'aktif'
             ORDER BY s.nama ASC"
        );
        $stmt->execute([$mapelId, $semester, $taId, $kelasId]);
        $rows = $stmt->fetchAll();

        // 3. Merge: If final attendance in 'nilai' table is NULL/default, use daily totals
        foreach ($rows as &$row) {
            $sid = $row['siswa_id'];
            if ($row['sakit'] == 0 && isset($dailyTotals[$sid]['s_harian'])) $row['sakit'] = $dailyTotals[$sid]['s_harian'];
            if ($row['izin'] == 0 && isset($dailyTotals[$sid]['i_harian'])) $row['izin'] = $dailyTotals[$sid]['i_harian'];
            if ($row['alfa'] == 0 && isset($dailyTotals[$sid]['a_harian'])) $row['alfa'] = $dailyTotals[$sid]['a_harian'];
        }

        return $rows;
    }

    /**
     * Batch save grades and attendance into the unified 'nilai' table
     */
    public function saveBatchNilai(int $mapelId, int $kelasId, int $semester, array $data): array
    {
        $taModel = new TahunAjaranModel();
        $activeYear = $taModel->getActive();
        $taId = $activeYear ? (int)$activeYear['id'] : 0;

        if ($taId === 0) {
            return ['success' => false, 'message' => 'Tahun ajaran aktif tidak ditemukan.'];
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                "INSERT INTO nilai (siswa_id, mapel_id, tahun_ajaran_id, semester, s1, s2, s3, pts, sakit, izin, alfa, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'lengkap')
                 ON DUPLICATE KEY UPDATE 
                    s1 = VALUES(s1),
                    s2 = VALUES(s2),
                    s3 = VALUES(s3),
                    pts = VALUES(pts),
                    sakit = VALUES(sakit),
                    izin = VALUES(izin),
                    alfa = VALUES(alfa),
                    status = 'lengkap'"
            );

            foreach ($data as $siswaId => $vals) {
                $stmt->execute([
                    (int)$siswaId,
                    $mapelId,
                    $taId,
                    $semester,
                    $vals['s1'] !== '' ? $vals['s1'] : null,
                    $vals['s2'] !== '' ? $vals['s2'] : null,
                    $vals['s3'] !== '' ? $vals['s3'] : null,
                    $vals['pts'] !== '' ? $vals['pts'] : null,
                    (int)($vals['sakit'] ?? 0),
                    (int)($vals['izin'] ?? 0),
                    (int)($vals['alfa'] ?? 0)
                ]);
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'Nilai dan absensi berhasil disimpan.'];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Gagal menyimpan data: ' . $e->getMessage()];
        }
    }

    /**
     * Get completion status for all mapels in a class
     */
    public function getMapelStatusDiKelas(int $kelasId, int $semester): array
    {
        $taModel = new TahunAjaranModel();
        $activeYear = $taModel->getActive();
        $taId = $activeYear ? (int)$activeYear['id'] : 0;

        $stmt = $this->db->prepare(
            "SELECT p.mapel_id, m.nama as mapel_nama, m.kategori,
                    CASE 
                        WHEN COUNT(DISTINCT s.id) = SUM(CASE WHEN n.status = 'lengkap' THEN 1 ELSE 0 END) THEN 'lengkap'
                        ELSE 'belum'
                    END as status
             FROM pengampuan p
             JOIN mapel m ON m.id = p.mapel_id
             JOIN siswa s ON s.kelas_id = p.kelas_id AND s.status = 'aktif'
             LEFT JOIN nilai n ON n.siswa_id = s.id 
                AND n.mapel_id = p.mapel_id 
                AND n.semester = ?
                AND n.tahun_ajaran_id = ?
             WHERE p.kelas_id = ? AND p.status = 'approved'
             GROUP BY p.mapel_id, m.nama, m.kategori
             ORDER BY m.kategori ASC, m.nama ASC"
        );
        $stmt->execute([$semester, $taId, $kelasId]);
        return $stmt->fetchAll();
    }

    /**
     * For Print: Get all subject grades for a specific student
     */
    public function getGradesBySiswa(int $siswaId, int $semester): array
    {
        $taModel = new TahunAjaranModel();
        $activeYear = $taModel->getActive();
        $taId = $activeYear ? (int)$activeYear['id'] : 0;

        $stmt = $this->db->prepare(
            "SELECT n.*, m.nama as mapel_nama, m.kategori, m.kktp as kkm
             FROM nilai n
             JOIN mapel m ON m.id = n.mapel_id
             WHERE n.siswa_id = ? AND n.semester = ? AND n.tahun_ajaran_id = ?
             ORDER BY m.kategori ASC, m.nama ASC"
        );
        $stmt->execute([$siswaId, $semester, $taId]);
        return $stmt->fetchAll();
    }

    public function getCatatanSiswa(int $siswaId, int $semester): array
    {
        $taModel = new TahunAjaranModel();
        $activeYear = $taModel->getActive();
        $taId = $activeYear ? (int)$activeYear['id'] : 0;

        $stmt = $this->db->prepare(
            "SELECT * FROM catatan_wali WHERE siswa_id = ? AND semester = ? AND tahun_ajaran_id = ? LIMIT 1"
        );
        $stmt->execute([$siswaId, $semester, $taId]);
        return $stmt->fetch() ?: ['sikap' => '', 'catatan' => ''];
    }

    public function saveCatatan(int $siswaId, int $semester, ?string $sikap, ?string $catatan): bool
    {
        $taModel = new TahunAjaranModel();
        $activeYear = $taModel->getActive();
        $taId = $activeYear ? (int)$activeYear['id'] : 0;

        $stmt = $this->db->prepare(
            "INSERT INTO catatan_wali (siswa_id, semester, tahun_ajaran_id, sikap, catatan)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE sikap = VALUES(sikap), catatan = VALUES(catatan)"
        );
        return $stmt->execute([$siswaId, $semester, $taId, $sikap, $catatan]);
    }

    public function getEkskulBySiswa(int $siswaId, int $semester): array
    {
        $taModel = new TahunAjaranModel();
        $activeYear = $taModel->getActive();
        $tahunAjaran = $activeYear ? $activeYear['nama'] : '';

        $stmt = $this->db->prepare(
            "SELECT m.nama as nama_kegiatan, 
                    CONCAT('Predikat: ', en.nilai, '. ', en.keterangan) as keterangan
             FROM ekskul_nilai en
             JOIN master_ekskul m ON m.id = en.ekskul_id
             WHERE en.siswa_id = ? AND en.semester = ? AND en.tahun_ajaran_id = ?"
        );
        $stmt->execute([$siswaId, $semester, $activeYear ? (int)$activeYear['id'] : 0]);
        return $stmt->fetchAll();
    }
}
