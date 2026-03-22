<?php

class DashboardController
{
    private UserModel $userModel;
    private KelasModel $kelasModel;
    private JurusanModel $jurusanModel;
    private NilaiModel $nilaiModel;
    private PengampuanModel $pengampuanModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->kelasModel = new KelasModel();
        $this->jurusanModel = new JurusanModel();
        $this->nilaiModel = new NilaiModel();
        $this->pengampuanModel = new PengampuanModel();
    }

    public function index(): void
    {
        Middleware::requireAuth();
        $userId = Session::getUserId();
        $roles = Session::getUserRoles();
        $user = $this->userModel->findById($userId);

        // Tandai semua notifikasi sebagai dibaca
        if (get_param('mark_read') === '1') {
            $this->userModel->markAllNotificationsRead($userId);
            redirect('?page=dashboard');
        }

        $data = [
            'title' => 'Dashboard – ' . APP_NAME,
            'pageTitle' => 'Dashboard',
            'user' => $user,
            'roles' => $roles,
            'notifications' => $this->userModel->getNotifications($userId, 5),
            'unread' => $this->userModel->countUnreadNotifications($userId),
        ];

        // Data berdasarkan role
        if (in_array(ROLE_ADMIN, $roles)) {
            $data = array_merge($data, $this->getAdminStats());
        }

        if (in_array(ROLE_GURU_MAPEL, $roles)) {
            $data['pengampuan_list'] = $this->pengampuanModel->getByGuru($userId);
        }

        if (in_array(ROLE_KAPROGLI, $roles)) {
            $data['jurusan_list'] = $this->jurusanModel->getByKaprogli($userId);
        }

        if (in_array(ROLE_WALI_KELAS, $roles)) {
            $data['kelas_wali_list'] = $this->kelasModel->getByWali($userId);
        }

        if (in_array(ROLE_PEMBINA_EKSKUL, $roles)) {
            $data['ekskul_list'] = (new EkskulModel())->getByPembina($userId);
        }

        renderView('dashboard/index', $data);
    }

    private function getAdminStats(): array
    {
        $db = getDB();

        $stats = $db->query("
            SELECT 
                (SELECT COUNT(*) FROM users) as totalUsers,
                (SELECT COUNT(*) FROM kelas) as totalKelas,
                (SELECT COUNT(*) FROM siswa) as totalSiswa,
                (SELECT COUNT(*) FROM kelas WHERE status = 'final') as totalFinal,
                (SELECT COUNT(*) FROM pengampuan WHERE status = 'approved') as totalPengampuan
        ")->fetch();
        
        return [
            'stat_users' => (int)($stats['totalUsers'] ?? 0),
            'stat_kelas' => (int)($stats['totalKelas'] ?? 0),
            'stat_siswa' => (int)($stats['totalSiswa'] ?? 0),
            'stat_final' => (int)($stats['totalFinal'] ?? 0),
            'stat_pengampuan' => (int)($stats['totalPengampuan'] ?? 0),
        ];
    }
}
