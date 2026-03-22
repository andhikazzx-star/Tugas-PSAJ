<?php

class LandingController
{
    public function index(): void
    {
        // Jika sudah login, langsung redirect ke dashboard
        if (Session::isLoggedIn()) {
            redirect('?page=dashboard');
        }

        // Tampilkan landing page
        require VIEWS_PATH . '/landing/index.php';
    }

    public function stats(): void
    {
        header('Content-Type: application/json');

        try {
            $db = getDB();

            $stats = $db->query("
                SELECT 
                    (SELECT COUNT(*) FROM siswa WHERE status = 'aktif') as siswa,
                    (SELECT COUNT(*) FROM kelas) as kelas,
                    (SELECT COUNT(DISTINCT guru_id) FROM pengampuan WHERE status = 'approved') as guru
            ")->fetch();

            echo json_encode([
                'siswa' => (int) ($stats['siswa'] ?? 0),
                'kelas' => (int) ($stats['kelas'] ?? 0),
                'guru' => (int) ($stats['guru'] ?? 0)
            ]);
        } catch (\Exception $e) {
            echo json_encode(['siswa' => 0, 'kelas' => 0, 'guru' => 0]);
        }
        exit;
    }
}
