<?php

class TahunAjaranModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM tahun_ajaran ORDER BY nama DESC");
        return $stmt->fetchAll();
    }

    public function getActive(): array|false
    {
        $stmt = $this->db->query("SELECT * FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
        return $stmt->fetch();
    }

    public function deactivateAll(): bool
    {
        return $this->db->query("UPDATE tahun_ajaran SET is_active = 0")->execute();
    }

    public function create(string $nama, bool $isActive = false): int
    {
        $stmt = $this->db->prepare("INSERT INTO tahun_ajaran (nama, is_active) VALUES (?, ?)");
        $stmt->execute([sanitize($nama), $isActive ? 1 : 0]);
        return (int) $this->db->lastInsertId();
    }
}
