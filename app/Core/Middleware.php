<?php

class Middleware
{
    /**
     * Memastikan user sudah login
     */
    public static function requireAuth(): void
    {
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'Silakan login terlebih dahulu.');
            redirect('?page=login');
        }
    }

    /**
     * Memastikan user memiliki salah satu role yang diperlukan
     */
    public static function requireRole(array $roles): void
    {
        self::requireAuth();
        if (!Session::hasAnyRole($roles)) {
            http_response_code(403);
            renderView('errors/403');
            exit;
        }
    }

    /**
     * Redirect jika sudah login
     */
    public static function requireGuest(): void
    {
        if (Session::isLoggedIn()) {
            redirect('?page=dashboard');
        }
    }

    /**
     * Cek apakah kelas masih bisa diedit (tidak final)
     */
    public static function requireKelasNotFinal(int $kelasId): void
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT status FROM kelas WHERE id = ?");
        $stmt->execute([$kelasId]);
        $kelas = $stmt->fetch();

        if (!$kelas || $kelas['status'] === STATUS_FINAL) {
            http_response_code(403);
            Session::flash('error', 'Nilai tidak dapat diubah. Kelas sudah difinalisasi.');
            redirect('?page=dashboard');
        }
    }

    /**
     * Cek kepemilikan pengampuan (guru hanya bisa akses mapel yang diajarkan)
     */
    public static function requirePengampuan(int $guruId, int $mapelId, int $kelasId): void
    {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT id FROM pengampuan 
             WHERE guru_id = ? AND mapel_id = ? AND kelas_id = ? AND status = 'approved'"
        );
        $stmt->execute([$guruId, $mapelId, $kelasId]);

        if (!$stmt->fetch()) {
            http_response_code(403);
            Session::flash('error', 'Akses ditolak. Anda tidak memiliki pengampuan untuk mapel/kelas ini.');
            redirect('?page=dashboard');
        }
    }
}
