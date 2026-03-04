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

            $siswa = (int) $db->query("SELECT COUNT(*) FROM siswa WHERE status = 'aktif'")->fetchColumn();
            $kelas = (int) $db->query("SELECT COUNT(*) FROM kelas")->fetchColumn();
            $guru = (int) $db->query("SELECT COUNT(DISTINCT guru_id) FROM pengampuan WHERE status = 'approved'")->fetchColumn();

            echo json_encode(compact('siswa', 'kelas', 'guru'));
        } catch (\Exception $e) {
            echo json_encode(['siswa' => 0, 'kelas' => 0, 'guru' => 0]);
        }
        exit;
    }
}
