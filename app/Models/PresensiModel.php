<?php

class PresensiModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /**
     * Get attendance for a specific session
     */
    public function getAttendance(int $pengampuanId, string $date): array
    {
        $stmt = $this->db->prepare(
            "SELECT s.id as siswa_id, s.nama as siswa_nama, s.nis,
                    am.status, am.pertemuan
             FROM siswa s
             JOIN pengampuan p ON p.kelas_id = s.kelas_id
             LEFT JOIN absensi_mapel am ON am.siswa_id = s.id AND am.pengampuan_id = p.id AND am.tanggal = ?
             WHERE p.id = ? AND s.status = 'aktif'
             ORDER BY s.nama ASC"
        );
        $stmt->execute([$date, $pengampuanId]);
        return $stmt->fetchAll();
    }

    /**
     * Save attendance for a session
     */
    public function saveAttendance(int $pengampuanId, int $semester, string $date, array $data): bool
    {
        try {
            $this->db->beginTransaction();

            foreach ($data as $siswaId => $status) {
                if (empty($status)) continue;

                $stmt = $this->db->prepare(
                    "INSERT INTO absensi_mapel (pengampuan_id, siswa_id, semester, tanggal, status)
                     VALUES (?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE status = VALUES(status)"
                );
                $stmt->execute([$pengampuanId, $siswaId, $semester, $date, $status]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Get summary counts (S, I, A) for a student in a specific pengampuan (mapel)
     */
    public function getSummary(int $pengampuanId, int $semester): array
    {
        $stmt = $this->db->prepare(
            "SELECT siswa_id,
                    SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa
             FROM absensi_mapel
             WHERE pengampuan_id = ? AND semester = ?
             GROUP BY siswa_id"
        );
        $stmt->execute([$pengampuanId, $semester]);
        return $stmt->fetchAll(PDO::FETCH_UNIQUE) ?: [];
    }
}
