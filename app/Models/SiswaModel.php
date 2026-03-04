<?php

class SiswaModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll(?int $kelasId = null, ?string $search = null): array
    {
        $sql = "SELECT s.*, k.nama as kelas_nama, j.nama as jurusan_nama, t.nama as tahun_ajaran_nama
                FROM siswa s
                LEFT JOIN kelas k ON k.id = s.kelas_id
                LEFT JOIN jurusan j ON j.id = k.jurusan_id
                LEFT JOIN tahun_ajaran t ON t.id = k.tahun_ajaran_id
                WHERE 1=1";
        $params = [];

        if ($kelasId) {
            $sql .= " AND s.kelas_id = ?";
            $params[] = $kelasId;
        }

        if ($search) {
            $sql .= " AND (s.nama LIKE ? OR s.nis LIKE ? OR s.nisn LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        $sql .= " ORDER BY s.nama ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT s.*, k.nama as kelas_nama, j.nama as jurusan_nama, t.nama as tahun_ajaran_nama, k.jurusan_id
             FROM siswa s
             LEFT JOIN kelas k ON k.id = s.kelas_id
             LEFT JOIN jurusan j ON j.id = k.jurusan_id
             LEFT JOIN tahun_ajaran t ON t.id = k.tahun_ajaran_id
             WHERE s.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByKelas(int $kelasId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM siswa WHERE kelas_id = ? AND status = 'aktif' ORDER BY nama ASC"
        );
        $stmt->execute([$kelasId]);
        return $stmt->fetchAll();
    }

    public function create(string $nama, string $nis, string $nisn, int $kelasId): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO siswa (nama, nis, nisn, kelas_id, status) VALUES (?, ?, ?, ?, 'aktif')"
        );
        $stmt->execute([sanitize($nama), sanitize($nis), sanitize($nisn), $kelasId]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $nama, string $nis, string $nisn, int $kelasId, string $status = 'aktif'): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE siswa SET nama = ?, nis = ?, nisn = ?, kelas_id = ?, status = ? WHERE id = ?"
        );
        return $stmt->execute([sanitize($nama), sanitize($nis), sanitize($nisn), $kelasId, $status, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM siswa WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
