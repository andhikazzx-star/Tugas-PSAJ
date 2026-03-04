<?php

class JurusanModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT j.*, 
                    COUNT(DISTINCT k.id) as total_kelas,
                    u.name as kaprogli_nama
             FROM jurusan j
             LEFT JOIN kelas k ON k.jurusan_id = j.id
             LEFT JOIN kaprogli_jurusan kj ON kj.jurusan_id = j.id
             LEFT JOIN users u ON u.id = kj.user_id
             GROUP BY j.id, j.nama, u.name
             ORDER BY j.nama ASC"
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM jurusan WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByKaprogli(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT j.*, COUNT(k.id) as total_kelas 
             FROM jurusan j
             JOIN kaprogli_jurusan kj ON kj.jurusan_id = j.id
             LEFT JOIN kelas k ON k.jurusan_id = j.id
             WHERE kj.user_id = ?
             GROUP BY j.id
             ORDER BY j.nama ASC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function create(string $nama): int
    {
        $stmt = $this->db->prepare("INSERT INTO jurusan (nama) VALUES (?)");
        $stmt->execute([sanitize($nama)]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $nama): bool
    {
        $stmt = $this->db->prepare("UPDATE jurusan SET nama = ? WHERE id = ?");
        return $stmt->execute([sanitize($nama), $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM jurusan WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function setKaprogli(int $jurusanId, int $userId): void
    {
        $stmt = $this->db->prepare("DELETE FROM kaprogli_jurusan WHERE jurusan_id = ?");
        $stmt->execute([$jurusanId]);
        $stmt = $this->db->prepare(
            "INSERT INTO kaprogli_jurusan (jurusan_id, user_id) VALUES (?, ?)"
        );
        $stmt->execute([$jurusanId, $userId]);
    }

    // Stats untuk kaprogli monitoring
    public function getMonitoringStats(int $jurusanId): array
    {
        $stmt = $this->db->prepare(
            "SELECT 
                COUNT(DISTINCT k.id) as total_kelas,
                COUNT(DISTINCT p.guru_id) as total_guru,
                COUNT(DISTINCT p.id) as total_pengampuan,
                COUNT(DISTINCT CASE WHEN n.status = 'lengkap' THEN CONCAT(n.mapel_id, '-', k2.id) END) as mapel_lengkap
             FROM kelas k
             LEFT JOIN pengampuan p ON p.kelas_id = k.id AND p.status = 'approved'
             LEFT JOIN kelas k2 ON k2.id = p.kelas_id
             LEFT JOIN siswa s ON s.kelas_id = k.id
             LEFT JOIN nilai n ON n.siswa_id = s.id AND n.mapel_id = p.mapel_id
             WHERE k.jurusan_id = ?"
        );
        $stmt->execute([$jurusanId]);
        return $stmt->fetch() ?: [];
    }

    public function getKelasProgressByJurusan(int $jurusanId): array
    {
        $stmt = $this->db->prepare(
            "SELECT k.id, k.nama, k.tahun_ajaran, k.status,
                    COUNT(DISTINCT p.id) as total_mapel,
                    COUNT(DISTINCT s.id) as total_siswa,
                    SUM(CASE WHEN n.status = 'lengkap' THEN 1 ELSE 0 END) as nilai_lengkap
             FROM kelas k
             LEFT JOIN pengampuan p ON p.kelas_id = k.id AND p.status = 'approved'
             LEFT JOIN siswa s ON s.kelas_id = k.id
             LEFT JOIN nilai n ON n.siswa_id = s.id AND n.mapel_id = p.mapel_id
             WHERE k.jurusan_id = ?
             GROUP BY k.id, k.nama, k.tahun_ajaran, k.status
             ORDER BY k.nama ASC"
        );
        $stmt->execute([$jurusanId]);
        return $stmt->fetchAll();
    }
}
