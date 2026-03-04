<?php

class PengampuanModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT p.*, u.name as guru_nama, m.nama as mapel_nama, k.nama as kelas_nama, 
                    j.nama as jurusan_nama, ta.nama as tahun_ajaran, k.status as kelas_status
             FROM pengampuan p
             JOIN users u ON u.id = p.guru_id
             JOIN mapel m ON m.id = p.mapel_id
             JOIN kelas k ON k.id = p.kelas_id
             LEFT JOIN jurusan j ON j.id = k.jurusan_id
             LEFT JOIN tahun_ajaran ta ON ta.id = k.tahun_ajaran_id
             ORDER BY k.nama ASC, m.nama ASC"
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, m.nama as mapel_nama, k.nama as kelas_nama, 
                    j.nama as jurusan_nama, ta.nama as tahun_ajaran, k.status as kelas_status
             FROM pengampuan p 
             JOIN mapel m ON m.id = p.mapel_id
             JOIN kelas k ON k.id = p.kelas_id
             LEFT JOIN jurusan j ON j.id = k.jurusan_id
             LEFT JOIN tahun_ajaran ta ON ta.id = k.tahun_ajaran_id
             WHERE p.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByGuru(int $guruId): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, m.nama as mapel_nama, k.nama as kelas_nama, 
                    j.nama as jurusan_nama, ta.nama as tahun_ajaran, k.status as kelas_status
             FROM pengampuan p
             JOIN mapel m ON m.id = p.mapel_id
             JOIN kelas k ON k.id = p.kelas_id
             LEFT JOIN jurusan j ON j.id = k.jurusan_id
             LEFT JOIN tahun_ajaran ta ON ta.id = k.tahun_ajaran_id
             WHERE p.guru_id = ? AND p.status = 'approved'
             ORDER BY k.nama ASC, m.nama ASC"
        );
        $stmt->execute([$guruId]);
        return $stmt->fetchAll();
    }

    public function checkOwnership(int $guruId, int $mapelId, int $kelasId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT id FROM pengampuan 
             WHERE guru_id = ? AND mapel_id = ? AND kelas_id = ? AND status = 'approved' LIMIT 1"
        );
        $stmt->execute([$guruId, $mapelId, $kelasId]);
        return (bool) $stmt->fetch();
    }

    public function create(int $guruId, int $mapelId, int $kelasId): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO pengampuan (guru_id, mapel_id, kelas_id, status) VALUES (?, ?, ?, 'approved')"
        );
        $stmt->execute([$guruId, $mapelId, $kelasId]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, int $guruId, int $mapelId, int $kelasId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE pengampuan SET guru_id = ?, mapel_id = ?, kelas_id = ? WHERE id = ?"
        );
        return $stmt->execute([$guruId, $mapelId, $kelasId, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM pengampuan WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function deleteAll(): bool
    {
        return $this->db->query("DELETE FROM pengampuan")->execute();
    }
}
