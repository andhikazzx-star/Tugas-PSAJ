<?php

class KelasModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT k.*, j.nama as jurusan_nama, u.name as wali_nama, t.nama as tahun_ajaran_nama
             FROM kelas k
             LEFT JOIN jurusan j ON j.id = k.jurusan_id
             LEFT JOIN wali_kelas wk ON wk.kelas_id = k.id
             LEFT JOIN users u ON u.id = wk.user_id
             LEFT JOIN tahun_ajaran t ON t.id = k.tahun_ajaran_id
             ORDER BY t.nama DESC, k.tingkat ASC, k.nama ASC"
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT k.*, j.nama as jurusan_nama, t.nama as tahun_ajaran_nama 
             FROM kelas k 
             LEFT JOIN jurusan j ON j.id = k.jurusan_id
             LEFT JOIN tahun_ajaran t ON t.id = k.tahun_ajaran_id
             WHERE k.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByWali(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT k.*, j.nama as jurusan_nama, t.nama as tahun_ajaran_nama,
                    COUNT(s.id) as total_siswa
             FROM kelas k
             JOIN wali_kelas wk ON wk.kelas_id = k.id
             LEFT JOIN jurusan j ON j.id = k.jurusan_id
             LEFT JOIN tahun_ajaran t ON t.id = k.tahun_ajaran_id
             LEFT JOIN siswa s ON s.kelas_id = k.id
             WHERE wk.user_id = ?
             GROUP BY k.id
             ORDER BY t.nama DESC, k.nama ASC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getWali(int $kelasId): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT u.* FROM users u
             JOIN wali_kelas wk ON wk.user_id = u.id
             WHERE wk.kelas_id = ? LIMIT 1"
        );
        $stmt->execute([$kelasId]);
        return $stmt->fetch();
    }

    public function create(string $nama, int $jurusanId, int $tingkat, int $tahunAjaranId): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO kelas (nama, jurusan_id, tingkat, tahun_ajaran_id, status) VALUES (?, ?, ?, ?, 'proses')"
        );
        $stmt->execute([sanitize($nama), $jurusanId, $tingkat, $tahunAjaranId]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $nama, int $jurusanId, int $tingkat, int $tahunAjaranId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE kelas SET nama = ?, jurusan_id = ?, tingkat = ?, tahun_ajaran_id = ? WHERE id = ?"
        );
        return $stmt->execute([sanitize($nama), $jurusanId, $tingkat, $tahunAjaranId, $id]);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE kelas SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM kelas WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function setWali(int $kelasId, int $userId): void
    {
        // Jika userId set ke 0 (Hapus Wali), cukup hapus mapping lama
        if ($userId === 0) {
            $stmt = $this->db->prepare("DELETE FROM wali_kelas WHERE kelas_id = ?");
            $stmt->execute([$kelasId]);
            return;
        }

        // Cek apakah user ini sudah menjadi wali di kelas LAIN pada tahun ajaran yang LINGKUPNYA aktif (opsional, tapi kita cek global saja sesuai permintaan 'hanya bisa mengurus 1 kelas')
        $stmtCheck = $this->db->prepare(
            "SELECT k.nama FROM wali_kelas wk 
             JOIN kelas k ON k.id = wk.kelas_id 
             WHERE wk.user_id = ? AND wk.kelas_id != ?"
        );
        $stmtCheck->execute([$userId, $kelasId]);
        $existing = $stmtCheck->fetch();

        if ($existing) {
            throw new Exception("Guru ini sudah menjadi Wali Kelas di " . $existing['nama'] . ". Satu guru hanya boleh mengurus 1 kelas.");
        }

        // Jalankan update/assign
        $stmt = $this->db->prepare("DELETE FROM wali_kelas WHERE kelas_id = ?");
        $stmt->execute([$kelasId]);

        $stmt = $this->db->prepare("INSERT INTO wali_kelas (kelas_id, user_id) VALUES (?, ?)");
        $stmt->execute([$kelasId, $userId]);
    }

    public function countNotFinal(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM kelas WHERE status != 'final'");
        return (int) $stmt->fetchColumn();
    }
}
