<?php

class MapelModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT m.*, j.nama as jurusan_nama 
             FROM mapel m 
             LEFT JOIN jurusan j ON j.id = m.jurusan_id 
             ORDER BY m.nama ASC"
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM mapel WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(string $nama, int $jurusanId): int
    {
        $stmt = $this->db->prepare("INSERT INTO mapel (nama, jurusan_id) VALUES (?, ?)");
        $stmt->execute([sanitize($nama), $jurusanId]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $nama, int $jurusanId): bool
    {
        $stmt = $this->db->prepare("UPDATE mapel SET nama = ?, jurusan_id = ? WHERE id = ?");
        return $stmt->execute([sanitize($nama), $jurusanId, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM mapel WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
