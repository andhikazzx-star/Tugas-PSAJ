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
        $stmt = $this->db->prepare(
            "SELECT s.id as siswa_id, s.nama as siswa_nama, s.nis,
                    n.id as nilai_id, n.pengetahuan, n.keterampilan, n.status,
                    k.sakit, k.izin, k.alfa
             FROM siswa s
             LEFT JOIN nilai n ON n.siswa_id = s.id AND n.mapel_id = ? AND n.semester = ?
             LEFT JOIN kehadiran k ON k.siswa_id = s.id AND k.mapel_id = ?
             WHERE s.kelas_id = ? AND s.status = 'aktif'
             ORDER BY s.nama ASC"
        );
        $stmt->execute([$mapelId, $semester, $mapelId, $kelasId]);
        return $stmt->fetchAll();
    }

    public function saveBatchNilai(int $mapelId, int $kelasId, int $semester, array $data): array
    {
        try {
            $this->db->beginTransaction();

            foreach ($data as $siswaId => $nilai) {
                // Save Nilai
                $stmt = $this->db->prepare(
                    "INSERT INTO nilai (siswa_id, mapel_id, semester, pengetahuan, keterampilan, status)
                     VALUES (?, ?, ?, ?, ?, 'lengkap')
                     ON DUPLICATE KEY UPDATE 
                        pengetahuan = VALUES(pengetahuan), 
                        keterampilan = VALUES(keterampilan),
                        status = 'lengkap'"
                );
                $stmt->execute([
                    $siswaId,
                    $mapelId,
                    $semester,
                    $nilai['pengetahuan'] !== '' ? $nilai['pengetahuan'] : null,
                    $nilai['keterampilan'] !== '' ? $nilai['keterampilan'] : null
                ]);

                // Save Kehadiran
                $stmtK = $this->db->prepare(
                    "INSERT INTO kehadiran (siswa_id, mapel_id, sakit, izin, alfa)
                     VALUES (?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE 
                        sakit = VALUES(sakit),
                        izin = VALUES(izin),
                        alfa = VALUES(alfa)"
                );
                $stmtK->execute([
                    $siswaId,
                    $mapelId,
                    (int) ($nilai['sakit'] ?? 0),
                    (int) ($nilai['izin'] ?? 0),
                    (int) ($nilai['alfa'] ?? 0)
                ]);
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'Nilai berhasil disimpan.'];
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
             LEFT JOIN kehadiran k ON k.siswa_id = n.siswa_id AND k.mapel_id = n.mapel_id
             WHERE n.siswa_id = ? AND n.semester = ?
             ORDER BY m.kategori ASC, m.nama ASC"
        );
        $stmt->execute([$siswaId, $semester]);
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
        // Table ekskul doesn't have semester column in db_structure.txt but requirements usually have it.
        // Reconstructing based on db_structure.txt first.
        $stmt = $this->db->prepare(
            "SELECT * FROM ekskul WHERE siswa_id = ?"
        );
        $stmt->execute([$siswaId]);
        return $stmt->fetchAll();
    }

    public function saveEkskul(int $siswaId, int $semester, array $data): void
    {
        try {
            $this->db->beginTransaction();
            $stmtDel = $this->db->prepare("DELETE FROM ekskul WHERE siswa_id = ?");
            $stmtDel->execute([$siswaId]);

            $stmtIns = $this->db->prepare(
                "INSERT INTO ekskul (siswa_id, nama_kegiatan, keterangan) VALUES (?, ?, ?)"
            );
            foreach ($data as $e) {
                $stmtIns->execute([$siswaId, $e['nama'], $e['keterangan']]);
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
