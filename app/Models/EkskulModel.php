<?php

class EkskulModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /**
     * Get all master extracurriculars
     */
    public function getAllMaster(): array
    {
        return $this->db->query("SELECT * FROM master_ekskul ORDER BY nama ASC")->fetchAll();
    }

    /**
     * Get ekskul assigned to a specific pembina
     */
    public function getByPembina(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT m.* FROM master_ekskul m 
             JOIN ekskul_pembina ep ON ep.ekskul_id = m.id 
             WHERE ep.user_id = ?"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getSiswaNilai(int $ekskulId, int $semester, string $tahunAjaran): array
    {
        return $this->getOnlyMembers($ekskulId, $semester, $tahunAjaran);
    }

    /**
     * Get ONLY students who are signed up for this ekskul
     */
    public function getOnlyMembers(int $ekskulId, int $semester, string $tahunAjaran): array
    {
        $stmt = $this->db->prepare(
            "SELECT s.id as siswa_id, s.nama as siswa_nama, k.nama as kelas_nama,
                    en.id as nilai_id, en.nilai, en.keterangan
             FROM ekskul_nilai en
             JOIN siswa s ON s.id = en.siswa_id
             JOIN kelas k ON k.id = s.kelas_id
             WHERE en.ekskul_id = ? 
                AND en.semester = ? 
                AND en.tahun_ajaran = ?
                AND s.status = 'aktif'
             ORDER BY k.nama ASC, s.nama ASC"
        );
        $stmt->execute([$ekskulId, $semester, $tahunAjaran]);
        return $stmt->fetchAll();
    }

    /**
     * Add member to ekskul (initialize with default grade B)
     */
    public function addMember(int $ekskulId, int $siswaId, int $semester, string $tahunAjaran): bool
    {
        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO ekskul_nilai (ekskul_id, siswa_id, semester, tahun_ajaran, nilai)
             VALUES (?, ?, ?, ?, 'B')"
        );
        return $stmt->execute([$ekskulId, $siswaId, $semester, $tahunAjaran]);
    }

    /**
     * Remove member from ekskul
     */
    public function removeMember(int $ekskulId, int $siswaId, int $semester, string $tahunAjaran): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM ekskul_nilai 
             WHERE ekskul_id = ? AND siswa_id = ? AND semester = ? AND tahun_ajaran = ?"
        );
        return $stmt->execute([$ekskulId, $siswaId, $semester, $tahunAjaran]);
    }

    /**
     * Save/Update ekskul grades in batch
     */
    public function saveBatchNilai(int $ekskulId, int $semester, string $tahunAjaran, array $data): bool
    {
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare(
                "INSERT INTO ekskul_nilai (ekskul_id, siswa_id, nilai, keterangan, semester, tahun_ajaran)
                 VALUES (?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE nilai = VALUES(nilai), keterangan = VALUES(keterangan)"
            );

            foreach ($data as $siswaId => $val) {
                // Only save if nilai or keterangan is provided
                if (!empty($val['nilai']) || !empty($val['keterangan'])) {
                    $stmt->execute([
                        $ekskulId,
                        $siswaId,
                        $val['nilai'] ?: 'B',
                        $val['keterangan'] ?: '',
                        $semester,
                        $tahunAjaran
                    ]);
                }
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function findMasterById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM master_ekskul WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Check if user is pembina of specific ekskul
     */
    public function isPembina(int $userId, int $ekskulId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM ekskul_pembina WHERE user_id = ? AND ekskul_id = ?");
        $stmt->execute([$userId, $ekskulId]);
        return (bool) $stmt->fetch();
    }

    public function createMaster(string $nama, ?string $pembinaNama = null): int
    {
        $stmt = $this->db->prepare("INSERT INTO master_ekskul (nama, pembina_nama) VALUES (?, ?)");
        $stmt->execute([sanitize($nama), $pembinaNama ? sanitize($pembinaNama) : null]);
        return (int) $this->db->lastInsertId();
    }

    public function updateMaster(int $id, string $nama, ?string $pembinaNama = null): bool
    {
        $stmt = $this->db->prepare("UPDATE master_ekskul SET nama = ?, pembina_nama = ? WHERE id = ?");
        return $stmt->execute([sanitize($nama), $pembinaNama ? sanitize($pembinaNama) : null, $id]);
    }

    public function deleteMaster(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM master_ekskul WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function setPembina(int $ekskulId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO ekskul_pembina (ekskul_id, user_id) VALUES (?, ?) 
             ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)"
        );
        return $stmt->execute([$ekskulId, $userId]);
    }

    public function removePembina(int $ekskulId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM ekskul_pembina WHERE ekskul_id = ?");
        return $stmt->execute([$ekskulId]);
    }

    public function getPembinas(): array
    {
        $stmt = $this->db->query(
            "SELECT ep.*, u.name as user_name 
             FROM ekskul_pembina ep
             JOIN users u ON u.id = ep.user_id"
        );
        $res = $stmt->fetchAll();
        $map = [];
        foreach ($res as $row) {
            $map[$row['ekskul_id']] = $row;
        }
        return $map;
    }
}
