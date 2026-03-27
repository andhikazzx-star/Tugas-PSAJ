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
             ORDER BY m.kategori ASC, m.nama ASC"
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM mapel WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(string $nama, int $jurusanId, string $kategori, int $kktp): int
    {
        $stmt = $this->db->prepare("INSERT INTO mapel (nama, jurusan_id, kategori, kktp) VALUES (?, ?, ?, ?)");
        $stmt->execute([sanitize($nama), $jurusanId, $kategori, $kktp]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $nama, int $jurusanId, string $kategori, int $kktp): bool
    {
        $stmt = $this->db->prepare("UPDATE mapel SET nama = ?, jurusan_id = ?, kategori = ?, kktp = ? WHERE id = ?");
        return $stmt->execute([sanitize($nama), $jurusanId, $kategori, $kktp, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM mapel WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
