<?php

class EkskulController
{
    private EkskulModel $ekskulModel;
    private TahunAjaranModel $taModel;

    public function __construct()
    {
        $this->ekskulModel = new EkskulModel();
        $this->taModel = new TahunAjaranModel();
    }

    /**
     * Dashboard/List Ekskul for Pembina (Anggota)
     */
    public function indexAnggota(): void
    {
        Middleware::requireRole([ROLE_PEMBINA_EKSKUL]);
        $userId = Session::getUserId();

        $ekskulList = $this->ekskulModel->getByPembina($userId);

        renderView('ekskul/index_anggota', [
            'title' => 'Input Anggota Ekskul – ' . APP_NAME,
            'ekskul_list' => $ekskulList
        ]);
    }

    /**
     * Dashboard/List Ekskul for Pembina (Nilai)
     */
    public function indexNilai(): void
    {
        Middleware::requireRole([ROLE_PEMBINA_EKSKUL]);
        $userId = Session::getUserId();

        $ekskulList = $this->ekskulModel->getByPembina($userId);

        renderView('ekskul/index_nilai', [
            'title' => 'Input Nilai Ekskul – ' . APP_NAME,
            'ekskul_list' => $ekskulList
        ]);
    }

    /**
     * Form Input Nilai Ekskul
     */
    public function inputForm(): void
    {
        Middleware::requireRole([ROLE_PEMBINA_EKSKUL, ROLE_ADMIN]);
        $userId = Session::getUserId();
        
        $ekskulId = (int) get_param('ekskul_id');
        $semester = (int) get_param('semester', 1);
        $tahunAjaran = $this->taModel->getActive();
        $taId = $tahunAjaran ? (int)$tahunAjaran['id'] : 0;

        if (!Session::hasRole(ROLE_ADMIN) && !$this->ekskulModel->isPembina($userId, $ekskulId)) {
            flashError('Akses ditolak. Anda bukan pembina untuk ekstrakurikuler ini.');
            redirect('?page=ekskul.nilai');
        }

        $ekskul = $this->ekskulModel->findMasterById($ekskulId);
        $siswaNilai = $this->ekskulModel->getSiswaNilai($ekskulId, $semester, $taId);

        renderView('ekskul/input', [
            'title' => 'Input Nilai Ekskul – ' . APP_NAME,
            'ekskul' => $ekskul,
            'siswa_nilai' => $siswaNilai,
            'semester' => $semester,
            'tahun_ajaran' => $tahunAjaran ? $tahunAjaran['nama'] : 'Unknown'
        ]);
    }

    /**
     * Save Nilai Ekskul
     */
    public function save(): void
    {
        Middleware::requireRole([ROLE_PEMBINA_EKSKUL, ROLE_ADMIN]);
        if (!isPost()) redirect('?page=ekskul.nilai');

        $userId = Session::getUserId();
        $ekskulId = (int) post('ekskul_id');
        $semester = (int) post('semester');
        $tahunAjaran = $this->taModel->getActive();
        $taId = $tahunAjaran ? (int)$tahunAjaran['id'] : 0;
        $nilaiData = post('nilai_ekskul', []);

        if (!Session::hasRole(ROLE_ADMIN) && !$this->ekskulModel->isPembina($userId, $ekskulId)) {
            flashError('Akses ditolak.');
            redirect('?page=ekskul.nilai');
        }

        $success = $this->ekskulModel->saveBatchNilai($ekskulId, $semester, $taId, $nilaiData);

        if ($success) {
            flashSuccess('Nilai ekstrakurikuler berhasil disimpan.');
        } else {
            flashError('Gagal menyimpan nilai ekstrakurikuler.');
        }

        redirect("?page=ekskul.input&ekskul_id={$ekskulId}&semester={$semester}");
    }

    /**
     * Manage Members (Add/Remove)
     */
    public function members(): void
    {
        Middleware::requireRole([ROLE_PEMBINA_EKSKUL, ROLE_ADMIN]);
        $userId = Session::getUserId();
        
        $ekskulId = (int) get_param('ekskul_id');
        $semester = (int) get_param('semester', 1);
        $tahunAjaran = $this->taModel->getActive();
        $taId = $tahunAjaran ? (int)$tahunAjaran['id'] : 0;

        if (!Session::hasRole(ROLE_ADMIN) && !$this->ekskulModel->isPembina($userId, $ekskulId)) {
            flashError('Akses ditolak.');
            redirect('?page=ekskul.anggota');
        }

        $ekskul = $this->ekskulModel->findMasterById($ekskulId);
        $members = $this->ekskulModel->getOnlyMembers($ekskulId, $semester, $taId);
        
        $kelasModel = new KelasModel();
        $kelasList = $kelasModel->getAll();
        
        // Search students to add by class
        $kelasFilter = (int) get_param('kelas_id', 0);
        $availableSiswa = [];
        if ($kelasFilter > 0) {
            $siswaModel = new SiswaModel();
            $availableSiswa = $siswaModel->getAll($kelasFilter);
        }

        renderView('ekskul/members', [
            'getOnlyMembers' => true,
            'title' => 'Kelola Anggota Ekskul – ' . APP_NAME,
            'ekskul' => $ekskul,
            'members' => $members,
            'available_siswa' => $availableSiswa,
            'kelas_list' => $kelasList,
            'kelas_filter' => $kelasFilter,
            'semester' => $semester,
            'tahun_ajaran' => $tahunAjaran ? $tahunAjaran['nama'] : 'Unknown'
        ]);
    }

    /**
     * Add Student to Ekskul
     */
    public function addMember(): void
    {
        Middleware::requireRole([ROLE_PEMBINA_EKSKUL, ROLE_ADMIN]);
        $userId = Session::getUserId();
        $ekskulId = (int) get_param('ekskul_id');
        $siswaId = (int) get_param('siswa_id');
        $semester = (int) get_param('semester', 1);
        
        $tahunAjaran = $this->taModel->getActive();
        $taId = $tahunAjaran ? (int)$tahunAjaran['id'] : 0;

        if (!Session::hasRole(ROLE_ADMIN) && !$this->ekskulModel->isPembina($userId, $ekskulId)) {
            flashError('Akses ditolak.');
            redirect('?page=ekskul.anggota');
        }

        $this->ekskulModel->addMember($ekskulId, $siswaId, $semester, $taId);
        flashSuccess('Siswa berhasil ditambahkan sebagai anggota.');
        redirect("?page=ekskul.members&ekskul_id={$ekskulId}&semester={$semester}");
    }

    /**
     * Remove Student from Ekskul
     */
    public function removeMember(): void
    {
        Middleware::requireRole([ROLE_PEMBINA_EKSKUL, ROLE_ADMIN]);
        $userId = Session::getUserId();
        $ekskulId = (int) get_param('ekskul_id');
        $siswaId = (int) get_param('siswa_id');
        $semester = (int) get_param('semester', 1);
        
        $tahunAjaran = $this->taModel->getActive();
        $taId = $tahunAjaran ? (int)$tahunAjaran['id'] : 0;

        if (!Session::hasRole(ROLE_ADMIN) && !$this->ekskulModel->isPembina($userId, $ekskulId)) {
            flashError('Akses ditolak.');
            redirect('?page=ekskul.anggota');
        }

        $this->ekskulModel->removeMember($ekskulId, $siswaId, $semester, $taId);
        flashSuccess('Siswa telah dihapus dari keanggotaan.');
        redirect("?page=ekskul.members&ekskul_id={$ekskulId}&semester={$semester}");
    }
}
