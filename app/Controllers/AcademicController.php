<?php

class AcademicController
{
    private JurusanModel $jurusanModel;
    private KelasModel $kelasModel;
    private MapelModel $mapelModel;
    private SiswaModel $siswaModel;
    private PengampuanModel $pengampuanModel;
    private UserModel $userModel;
    private TahunAjaranModel $tahunAjaranModel;
    private EkskulModel $ekskulModel;

    public function __construct()
    {
        $this->jurusanModel = new JurusanModel();
        $this->kelasModel = new KelasModel();
        $this->mapelModel = new MapelModel();
        $this->siswaModel = new SiswaModel();
        $this->pengampuanModel = new PengampuanModel();
        $this->userModel = new UserModel();
        $this->tahunAjaranModel = new TahunAjaranModel();
        $this->ekskulModel = new EkskulModel();
    }

    // ======================== JURUSAN ========================

    public function jurusan(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        renderView('admin/jurusan', [
            'title' => 'Manajemen Jurusan – ' . APP_NAME,
            'jurusan_list' => $this->jurusanModel->getAll(),
            'kaprogli_list' => $this->userModel->getByRole(ROLE_KAPROGLI),
        ]);
    }

    public function createJurusan(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.jurusan');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.jurusan');
        }
        $nama = post('nama');
        $kaprogliId = (int) post('kaprogli_id', 0);

        if (empty($nama)) {
            flashError('Nama jurusan wajib diisi.');
            redirect('?page=admin.jurusan');
        }

        $id = $this->jurusanModel->create($nama);
        if ($kaprogliId > 0)
            $this->jurusanModel->setKaprogli($id, $kaprogliId);

        flashSuccess("Jurusan {$nama} berhasil ditambahkan.");
        redirect('?page=admin.jurusan');
    }

    public function updateJurusan(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.jurusan');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.jurusan');
        }
        $id = (int) post('jurusan_id');
        $nama = post('nama');
        $kaprogliId = (int) post('kaprogli_id', 0);

        if (empty($nama)) {
            flashError('Nama jurusan wajib diisi.');
            redirect('?page=admin.jurusan');
        }
        $this->jurusanModel->update($id, $nama);
        if ($kaprogliId > 0)
            $this->jurusanModel->setKaprogli($id, $kaprogliId);

        flashSuccess('Jurusan berhasil diperbarui.');
        redirect('?page=admin.jurusan');
    }

    public function deleteJurusan(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.jurusan');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.jurusan');
        }
        $this->jurusanModel->delete((int) post('jurusan_id'));
        flashSuccess('Jurusan berhasil dihapus.');
        redirect('?page=admin.jurusan');
    }

    // ======================== KELAS ========================

    public function kelas(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        renderView('admin/kelas', [
            'title' => 'Manajemen Kelas – ' . APP_NAME,
            'kelas_list' => $this->kelasModel->getAll(),
            'jurusan_list' => $this->jurusanModel->getAll(),
            'wali_list' => $this->userModel->getByRole(ROLE_WALI_KELAS),
            'tahun_ajaran_list' => $this->tahunAjaranModel->getAll(),
        ]);
    }

    public function createKelas(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.kelas');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.kelas');
        }
        $nama = post('nama');
        $jurusanId = (int) post('jurusan_id');
        $tingkat = (int) post('tingkat');
        $tahunAjaranId = (int) post('tahun_ajaran_id');
        $waliId = (int) post('wali_id', 0);

        if (empty($nama) || !$jurusanId || !$tingkat || !$tahunAjaranId) {
            flashError('Semua field wajib diisi.');
            redirect('?page=admin.kelas');
        }

        $id = $this->kelasModel->create($nama, $jurusanId, $tingkat, $tahunAjaranId);

        try {
            if ($waliId > 0) {
                $this->kelasModel->setWali($id, $waliId);
            }
            flashSuccess("Kelas {$nama} berhasil ditambahkan.");
        } catch (Exception $e) {
            flashError("Kelas berhasil dibuat, namun: " . $e->getMessage());
        }

        redirect('?page=admin.kelas');
    }

    public function updateKelas(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.kelas');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.kelas');
        }
        $id = (int) post('kelas_id');
        $nama = post('nama');
        $jurusanId = (int) post('jurusan_id');
        $tingkat = (int) post('tingkat');
        $tahunAjaranId = (int) post('tahun_ajaran_id');
        $waliId = (int) post('wali_id', 0);

        if (empty($nama) || !$jurusanId || !$tingkat || !$tahunAjaranId) {
            flashError('Semua field wajib diisi.');
            redirect('?page=admin.kelas');
        }

        $this->kelasModel->update($id, $nama, $jurusanId, $tingkat, $tahunAjaranId);

        try {
            $this->kelasModel->setWali($id, $waliId);
            flashSuccess('Kelas berhasil diperbarui.');
        } catch (Exception $e) {
            flashError('Kelas diperbarui, namun gagal set wali: ' . $e->getMessage());
        }

        redirect('?page=admin.kelas');
    }

    public function deleteKelas(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.kelas');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.kelas');
        }
        $this->kelasModel->delete((int) post('kelas_id'));
        flashSuccess('Kelas berhasil dihapus.');
        redirect('?page=admin.kelas');
    }

    // ======================== MAPEL ========================

    public function mapel(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        renderView('admin/mapel', [
            'title' => 'Manajemen Mata Pelajaran – ' . APP_NAME,
            'mapel_list' => $this->mapelModel->getAll(),
            'jurusan_list' => $this->jurusanModel->getAll(),
        ]);
    }

    public function createMapel(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.mapel');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.mapel');
        }
        $nama = post('nama');
        $jurusanId = (int) post('jurusan_id');
        if (empty($nama) || !$jurusanId) {
            flashError('Semua field wajib diisi.');
            redirect('?page=admin.mapel');
        }
        $this->mapelModel->create($nama, $jurusanId);
        flashSuccess("Mata pelajaran {$nama} berhasil ditambahkan.");
        redirect('?page=admin.mapel');
    }

    public function updateMapel(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.mapel');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.mapel');
        }
        $id = (int) post('mapel_id');
        $nama = post('nama');
        $jurusanId = (int) post('jurusan_id');
        if (empty($nama) || !$jurusanId) {
            flashError('Semua field wajib diisi.');
            redirect('?page=admin.mapel');
        }
        $this->mapelModel->update($id, $nama, $jurusanId);
        flashSuccess('Mata pelajaran berhasil diperbarui.');
        redirect('?page=admin.mapel');
    }

    public function deleteMapel(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.mapel');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.mapel');
        }
        $this->mapelModel->delete((int) post('mapel_id'));
        flashSuccess('Mata pelajaran berhasil dihapus.');
        redirect('?page=admin.mapel');
    }

    // ======================== SISWA ========================

    public function siswa(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        $kelasId = get_param('kelas_id') ?: null;
        $search = get_param('q') ?: null;

        renderView('admin/siswa', [
            'title' => 'Manajemen Siswa – ' . APP_NAME,
            'siswa_list' => $this->siswaModel->getAll($kelasId, $search),
            'kelas_list' => $this->kelasModel->getAll(),
        ]);
    }

    public function createSiswa(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.siswa');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.siswa');
        }
        $nama = post('nama');
        $nis = post('nis');
        $nisn = post('nisn');
        $kelasId = (int) post('kelas_id');

        if (empty($nama) || !$kelasId) {
            flashError('Nama dan Kelas wajib diisi.');
            redirect('?page=admin.siswa');
        }
        $this->siswaModel->create($nama, $nis, $nisn, $kelasId);
        flashSuccess("Siswa {$nama} berhasil ditambahkan.");
        redirect('?page=admin.siswa');
    }

    public function updateSiswa(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.siswa');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.siswa');
        }
        $id = (int) post('siswa_id');
        $nama = post('nama');
        $nis = post('nis');
        $nisn = post('nisn');
        $kelasId = (int) post('kelas_id');
        $status = post('status', 'aktif');

        if (empty($nama) || !$kelasId) {
            flashError('Nama dan Kelas wajib diisi.');
            redirect('?page=admin.siswa');
        }
        $this->siswaModel->update($id, $nama, $nis, $nisn, $kelasId, $status);
        flashSuccess('Data siswa berhasil diperbarui.');
        redirect('?page=admin.siswa');
    }

    public function deleteSiswa(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.siswa');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.siswa');
        }
        $this->siswaModel->delete((int) post('siswa_id'));
        flashSuccess('Siswa berhasil dihapus.');
        redirect('?page=admin.siswa');
    }

    public function importSiswa(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.siswa');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.siswa');
        }

        $kelasId = (int) post('kelas_id');
        $csvData = post('csv_data');

        if (!$kelasId || empty($csvData)) {
            flashError('Kelas dan Data CSV wajib diisi.');
            redirect('?page=admin.siswa');
        }

        $lines = explode("\n", str_replace("\r", "", $csvData));
        $count = 0;
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line))
                continue;

            $data = preg_split('/[,;\t]/', $line);
            $nama = trim($data[0] ?? '');
            $nis = trim($data[1] ?? '');
            $nisn = trim($data[2] ?? '');

            if (!empty($nama)) {
                $this->siswaModel->create($nama, $nis, $nisn, $kelasId);
                $count++;
            }
        }

        flashSuccess("Berhasil mengimpor {$count} siswa.");
        redirect('?page=admin.siswa&kelas_id=' . $kelasId);
    }

    // ======================== PENGAMPUAN ========================

    public function pengampuan(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        renderView('admin/pengampuan', [
            'title' => 'Manajemen Pengampuan – ' . APP_NAME,
            'pengampuan_list' => $this->pengampuanModel->getAll(),
            'guru_list' => $this->userModel->getByRole(ROLE_GURU_MAPEL),
            'mapel_list' => $this->mapelModel->getAll(),
            'kelas_list' => $this->kelasModel->getAll(),
        ]);
    }

    public function createPengampuan(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.pengampuan');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.pengampuan');
        }
        $guruId = (int) post('guru_id');
        $mapelId = (int) post('mapel_id');
        $kelasId = (int) post('kelas_id');
        if (!$guruId || !$mapelId || !$kelasId) {
            flashError('Semua field wajib diisi.');
            redirect('?page=admin.pengampuan');
        }
        $this->pengampuanModel->create($guruId, $mapelId, $kelasId);
        flashSuccess('Pengampuan berhasil ditambahkan.');
        redirect('?page=admin.pengampuan');
    }

    public function deletePengampuan(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.pengampuan');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.pengampuan');
        }
        $this->pengampuanModel->delete((int) post('pengampuan_id'));
        flashSuccess('Pengampuan berhasil dihapus.');
        redirect('?page=admin.pengampuan');
    }

    public function updatePengampuan(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.pengampuan');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.pengampuan');
        }
        $id = (int) post('pengampuan_id');
        $guruId = (int) post('guru_id');
        $mapelId = (int) post('mapel_id');
        $kelasId = (int) post('kelas_id');

        if (!$id || !$guruId || !$mapelId || !$kelasId) {
            flashError('Semua field wajib diisi.');
            redirect('?page=admin.pengampuan');
        }

        $this->pengampuanModel->update($id, $guruId, $mapelId, $kelasId);
        flashSuccess('Pengampuan berhasil diperbarui.');
        redirect('?page=admin.pengampuan');
    }

    // ======================== EKSKUL ========================

    public function ekskul(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        renderView('admin/ekskul/index', [
            'title' => 'Manajemen Ekskul – ' . APP_NAME,
            'ekskul_list' => $this->ekskulModel->getAllMaster()
        ]);
    }

    public function createEkskul(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost()) redirect('?page=admin.ekskul');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.ekskul');
        }
        $nama = post('nama');
        $pembinaNama = trim(post('pembina_nama') ?? '');

        if (empty($nama)) {
            flashError('Nama ekskul wajib diisi.');
            redirect('?page=admin.ekskul');
        }

        $id = $this->ekskulModel->createMaster($nama, $pembinaNama);
        $email = '-';

        if (!empty($pembinaNama)) {
            $cleanName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $pembinaNama));
            $email = $cleanName . '@smkn10sby.sch.id';
            
            $user = $this->userModel->findByEmail($email);
            if (!$user) {
                $userId = $this->userModel->create($pembinaNama, $email, 'Password123');
                $roleId = $this->userModel->getRoleIdByName(ROLE_PEMBINA_EKSKUL);
                if ($roleId) $this->userModel->assignRole($userId, $roleId);
            } else {
                $userId = $user['id'];
                $roleId = $this->userModel->getRoleIdByName(ROLE_PEMBINA_EKSKUL);
                if ($roleId) $this->userModel->assignRole($userId, $roleId);
            }
            $this->ekskulModel->setPembina($id, $userId);
        }

        flashSuccess("Ekskul {$nama} berhasil ditambahkan. (Email Pembina: {$email})");
        redirect('?page=admin.ekskul');
    }

    public function updateEkskul(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost()) redirect('?page=admin.ekskul');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.ekskul');
        }
        $id = (int) post('ekskul_id');
        $nama = post('nama');
        $pembinaNama = trim(post('pembina_nama') ?? '');

        if (empty($nama)) {
            flashError('Nama ekskul wajib diisi.');
            redirect('?page=admin.ekskul');
        }
        $this->ekskulModel->updateMaster($id, $nama, $pembinaNama);
        $email = '';

        if (!empty($pembinaNama)) {
            $cleanName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $pembinaNama));
            $email = $cleanName . '@smkn10sby.sch.id';
            
            $user = $this->userModel->findByEmail($email);
            if (!$user) {
                $userId = $this->userModel->create($pembinaNama, $email, 'Password123');
                $roleId = $this->userModel->getRoleIdByName(ROLE_PEMBINA_EKSKUL);
                if ($roleId) $this->userModel->assignRole($userId, $roleId);
            } else {
                $userId = $user['id'];
                $roleId = $this->userModel->getRoleIdByName(ROLE_PEMBINA_EKSKUL);
                if ($roleId) $this->userModel->assignRole($userId, $roleId);
            }
            $this->ekskulModel->setPembina($id, $userId);
        } else {
            $this->ekskulModel->removePembina($id);
        }

        flashSuccess('Ekskul berhasil diperbarui.' . (!empty($email) ? " (Email Pembina: {$email})" : ''));
        redirect('?page=admin.ekskul');
    }

    public function deleteEkskul(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost()) redirect('?page=admin.ekskul');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.ekskul');
        }
        $this->ekskulModel->deleteMaster((int) post('ekskul_id'));
        flashSuccess('Ekskul berhasil dihapus.');
        redirect('?page=admin.ekskul');
    }

    public function inputEkskul(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        $ekskulId = (int) get_param('ekskul_id');
        $semester = (int) get_param('semester', 1);
        $taActive = $this->tahunAjaranModel->getActive();
        $taId = $taActive ? (int)$taActive['id'] : 0;
        $tahunAjaran = $taActive ? $taActive['nama'] : '2024/2025';

        $ekskul = $this->ekskulModel->findMasterById($ekskulId);
        $siswaNilai = $this->ekskulModel->getSiswaNilai($ekskulId, $semester, $taId);

        renderView('admin/ekskul/input', [
            'title' => 'Input Nilai Ekskul – ' . APP_NAME,
            'ekskul' => $ekskul,
            'siswa_nilai' => $siswaNilai,
            'semester' => $semester,
            'tahun_ajaran' => $tahunAjaran
        ]);
    }

    public function saveEkskul(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost()) redirect('?page=admin.ekskul');

        $ekskulId = (int) post('ekskul_id');
        $semester = (int) post('semester');
        $taActive = $this->tahunAjaranModel->getActive();
        $taId = $taActive ? (int)$taActive['id'] : 0;
        $nilaiData = post('nilai_ekskul', []);

        $success = $this->ekskulModel->saveBatchNilai($ekskulId, $semester, $taId, $nilaiData);

        if ($success) {
            flashSuccess('Nilai ekstrakurikuler berhasil disimpan.');
        } else {
            flashError('Gagal menyimpan nilai ekstrakurikuler.');
        }

        redirect("?page=admin.ekskul.input&ekskul_id={$ekskulId}&semester={$semester}");
    }

    public function membersEkskul(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        $ekskulId = (int) get_param('ekskul_id');
        $semester = (int) get_param('semester', 1);
        $taActive = $this->tahunAjaranModel->getActive();
        $taId = $taActive ? (int)$taActive['id'] : 0;
        $tahunAjaran = $taActive ? $taActive['nama'] : '2024/2025';

        $ekskul = $this->ekskulModel->findMasterById($ekskulId);
        $members = $this->ekskulModel->getOnlyMembers($ekskulId, $semester, $taId);
        
        $kelasModel = new KelasModel();
        $kelasList = $kelasModel->getAll();
        
        $kelasFilter = (int) get_param('kelas_id', 0);
        $availableSiswa = [];
        if ($kelasFilter > 0) {
            $availableSiswa = $this->siswaModel->getAll($kelasFilter);
        }

        renderView('admin/ekskul/members', [
            'getOnlyMembers' => true,
            'title' => 'Kelola Anggota Ekskul – ' . APP_NAME,
            'ekskul' => $ekskul,
            'members' => $members,
            'available_siswa' => $availableSiswa,
            'kelas_list' => $kelasList,
            'kelas_filter' => $kelasFilter,
            'semester' => $semester,
            'tahun_ajaran' => $tahunAjaran
        ]);
    }

    public function addMemberEkskul(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        $ekskulId = (int) get_param('ekskul_id');
        $siswaId = (int) get_param('siswa_id');
        $semester = (int) get_param('semester', 1);
        
        $taActive = $this->tahunAjaranModel->getActive();
        $taId = $taActive ? (int)$taActive['id'] : 0;

        $this->ekskulModel->addMember($ekskulId, $siswaId, $semester, $taId);
        flashSuccess('Siswa berhasil ditambahkan sebagai anggota.');
        redirect("?page=admin.ekskul.members&ekskul_id={$ekskulId}&semester={$semester}");
    }

    public function removeMemberEkskul(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        $ekskulId = (int) get_param('ekskul_id');
        $siswaId = (int) get_param('siswa_id');
        $semester = (int) get_param('semester', 1);
        
        $taActive = $this->tahunAjaranModel->getActive();
        $taId = $taActive ? (int)$taActive['id'] : 0;

        $this->ekskulModel->removeMember($ekskulId, $siswaId, $semester, $taId);
        flashSuccess('Siswa telah dihapus dari keanggotaan.');
        redirect("?page=admin.ekskul.members&ekskul_id={$ekskulId}&semester={$semester}");
    }
}
