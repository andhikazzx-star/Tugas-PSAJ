<?php

class NilaiController
{
    private NilaiModel $nilaiModel;
    private PengampuanModel $pengampuanModel;
    private KelasModel $kelasModel;
    private SiswaModel $siswaModel;
    private UserModel $userModel;

    public function __construct()
    {
        $this->nilaiModel = new NilaiModel();
        $this->pengampuanModel = new PengampuanModel();
        $this->kelasModel = new KelasModel();
        $this->siswaModel = new SiswaModel();
        $this->userModel = new UserModel();
    }

    /**
     * Guru: Pilih kelas & mapel untuk input nilai
     */
    public function inputForm(): void
    {
        Middleware::requireRole([ROLE_GURU_MAPEL, ROLE_WALI_KELAS]);
        $userId = Session::getUserId();

        $pengampuanList = $this->pengampuanModel->getByGuru($userId);

        $selectedPengampuanId = (int) get_param('pengampuan_id', 0);
        $semester = (int) get_param('semester', 1);

        $siswaNilai = [];
        $kelas = null;
        $selectedPengampuan = null;

        if ($selectedPengampuanId > 0) {
            // Validasi ownership pengampuan
            $selectedPengampuan = $this->pengampuanModel->findById($selectedPengampuanId);

            if (!$selectedPengampuan || (int) $selectedPengampuan['guru_id'] !== $userId) {
                flashError('Akses ditolak. Anda tidak memiliki hak akses untuk pengampuan ini.');
                redirect('?page=nilai');
            }

            // Validasi semester
            if (!in_array($semester, [1, 2])) {
                $semester = 1;
            }

            $kelas = $this->kelasModel->findById((int) $selectedPengampuan['kelas_id']);
            $siswaNilai = $this->nilaiModel->getByMapelKelas(
                (int) $selectedPengampuan['mapel_id'],
                (int) $selectedPengampuan['kelas_id'],
                $semester
            );
        }

        renderView('nilai/input', [
            'title' => 'Input Nilai – ' . APP_NAME,
            'pengampuan_list' => $pengampuanList,
            'selected_pengampuan' => $selectedPengampuan,
            'selected_id' => $selectedPengampuanId,
            'semester' => $semester,
            'siswaNilai' => $siswaNilai,
            'kelas' => $kelas,
        ]);
    }

    /**
     * Guru: Proses simpan nilai (POST)
     */
    public function saveNilai(): void
    {
        Middleware::requireRole([ROLE_GURU_MAPEL, ROLE_WALI_KELAS]);

        if (!isPost())
            redirect('?page=nilai');

        // Validasi CSRF
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token keamanan tidak valid.');
            redirect('?page=nilai');
        }

        $userId = Session::getUserId();
        $pengampuanId = (int) post('pengampuan_id');
        $semester = (int) post('semester');
        $nilaiData = post('nilai', []);

        // Validate pengampuan ownership
        $pengampuan = $this->pengampuanModel->findById($pengampuanId);
        if (!$pengampuan || (int) $pengampuan['guru_id'] !== $userId) {
            flashError('Akses ditolak.');
            redirect('?page=nilai');
        }

        if (!in_array($semester, [1, 2])) {
            flashError('Semester tidak valid.');
            redirect('?page=nilai&pengampuan_id=' . $pengampuanId . '&semester=' . $semester);
        }

        // Validate ownership via pengampuan table (double-check)
        $isOwner = $this->pengampuanModel->checkOwnership(
            $userId,
            (int) $pengampuan['mapel_id'],
            (int) $pengampuan['kelas_id']
        );

        if (!$isOwner) {
            flashError('Akses ditolak. Verifikasi pengampuan gagal.');
            redirect('?page=nilai');
        }

        // Simpan batch nilai
        $result = $this->nilaiModel->saveBatchNilai(
            (int) $pengampuan['mapel_id'],
            (int) $pengampuan['kelas_id'],
            $semester,
            is_array($nilaiData) ? $nilaiData : []
        );

        if ($result['success']) {
            // Kirim notifikasi ke wali kelas
            $this->notifyWaliKelas((int) $pengampuan['kelas_id'], $pengampuan['mapel_nama'] ?? '');
            flashSuccess($result['message']);
        } else {
            flashError($result['message']);
        }

        redirect('?page=nilai&pengampuan_id=' . $pengampuanId . '&semester=' . $semester);
    }

    /**
     * Wali Kelas: Monitoring nilai per kelas
     */
    public function monitoring(): void
    {
        Middleware::requireRole([ROLE_WALI_KELAS]);
        $userId = Session::getUserId();

        $kelasList = $this->kelasModel->getByWali($userId);
        $selectedKelasId = (int) get_param('kelas_id', 0);
        $semester = (int) get_param('semester', 1);

        if (!in_array($semester, [1, 2]))
            $semester = 1;

        $mapelStatus = [];
        $siswaList = [];
        $kelas = null;
        $totalBelumLengkap = 0;

        if ($selectedKelasId > 0) {
            // Validasi kelas adalah wali kelas ini
            $isWali = false;
            foreach ($kelasList as $k) {
                if ((int) $k['id'] === $selectedKelasId) {
                    $isWali = true;
                    $kelas = $k;
                    break;
                }
            }

            if (!$isWali) {
                flashError('Akses ditolak.');
                redirect('?page=monitoring');
            }

            $mapelStatus = $this->nilaiModel->getMapelStatusDiKelas($selectedKelasId, $semester);
            foreach ($mapelStatus as $ms) {
                if ($ms['status'] !== 'lengkap')
                    $totalBelumLengkap++;
            }

            $siswaList = $this->siswaModel->getByKelas($selectedKelasId);

            // Ambil catatan dan ekskul untuk setiap siswa
            foreach ($siswaList as &$s) {
                $cw = $this->nilaiModel->getCatatanSiswa((int) $s['id'], $semester);
                $s['sikap'] = $cw['sikap'];
                $s['catatan'] = $cw['catatan'];
                $s['ekskul'] = $this->nilaiModel->getEkskulBySiswa((int) $s['id'], $semester);
            }
        }

        renderView('nilai/monitoring', [
            'title' => 'Monitoring Nilai – ' . APP_NAME,
            'kelas_list' => $kelasList,
            'selected_kelas_id' => $selectedKelasId,
            'semester' => $semester,
            'mapel_status' => $mapelStatus,
            'siswa_list' => $siswaList,
            'kelas' => $kelas,
            'total_belum_lengkap' => $totalBelumLengkap,
        ]);
    }

    /**
     * Wali Kelas: Cetak Rapor Sisipan per Siswa
     */
    public function printRapor(): void
    {
        Middleware::requireRole([ROLE_WALI_KELAS]);

        $siswaId = (int) get_param('siswa_id');
        $semester = (int) get_param('semester', 1);

        if (!$siswaId) {
            flashError('Data siswa tidak ditemukan.');
            redirect('?page=monitoring');
        }

        $siswa = $this->siswaModel->findById($siswaId);
        if (!$siswa) {
            flashError('Siswa tidak ditemukan.');
            redirect('?page=monitoring');
        }

        // Validasi: Apakah user ini wali kelas dari siswa tersebut?
        $userId = Session::getUserId();

        // Cek apakah user adalah wali kelas dari kelas siswa tersebut
        $stmt = getDB()->prepare("SELECT id FROM wali_kelas WHERE kelas_id = ? AND user_id = ?");
        $stmt->execute([(int) $siswa['kelas_id'], $userId]);

        if (!$stmt->fetch()) {
            flashError('Akses ditolak. Anda hanya dapat mencetak rapor untuk siswa di kelas perwalian Anda.');
            redirect('?page=monitoring');
        }

        $grades = $this->nilaiModel->getGradesBySiswa($siswaId, $semester);
        $cw = $this->nilaiModel->getCatatanSiswa($siswaId, $semester);
        $ekskul = $this->nilaiModel->getEkskulBySiswa($siswaId, $semester);

        // Fetch Homeroom Teacher (Wali Kelas) details
        $wali = $this->kelasModel->getWali((int) $siswa['kelas_id']);

        // Data for template
        $data = [
            'siswa' => $siswa,
            'grades' => $grades,
            'sikap' => $cw['sikap'],
            'catatan' => $cw['catatan'],
            'ekskul' => $ekskul,
            'semester' => $semester,
            'wali' => $wali
        ];

        // Render view khusus print
        if (get_param('format') === 'pdf') {
            $this->generatePdf('nilai/print_sisipan', $data, "Rapor_Sisipan_{$siswa['nama']}.pdf");
        } else {
            require_once VIEWS_PATH . '/nilai/print_sisipan.php';
        }
    }

    /**
     * Helper to generate PDF using Dompdf
     */
    private function generatePdf(string $viewPath, array $data, string $filename): void
    {
        try {
            // Start output buffering
            ob_start();
            require VIEWS_PATH . '/' . $viewPath . '.php';
            $html = ob_get_clean();

            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('chroot', ROOT_PATH);
            $options->set('defaultFont', 'Times-Roman');

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->setProtocol(''); // Required for some environments to handle local absolute paths
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream($filename, ["Attachment" => false]);
            exit;
        } catch (Exception $e) {
            ob_end_clean();
            die("Gagal membuat PDF: " . $e->getMessage());
        }
    }

    /**
     * Wali Kelas: Simpan Catatan Wali Kelas (AJAX/POST)
     */
    public function saveCatatan(): void
    {
        Middleware::requireRole([ROLE_WALI_KELAS]);

        if (!isPost())
            redirect('?page=monitoring');

        $siswaId = (int) post('siswa_id');
        $semester = (int) post('semester', 1);
        $sikap = post('sikap');
        $catatan = post('catatan');

        // Validasi: Apakah user ini wali kelas dari siswa tersebut?
        $userId = Session::getUserId();
        $siswa = $this->siswaModel->findById($siswaId);
        if (!$siswa) {
            echo json_encode(['success' => false, 'message' => 'Siswa tidak ditemukan.']);
            return;
        }

        $isWali = $this->checkWaliAccess((int) $siswa['kelas_id'], $userId);
        if (!$isWali) {
            echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        $result = $this->nilaiModel->saveCatatan($siswaId, $semester, $sikap, $catatan);

        echo json_encode(['success' => $result, 'message' => $result ? 'Catatan dan sikap berhasil disimpan.' : 'Gagal menyimpan data.']);
        exit;
    }

    /**
     * Wali Kelas: Simpan Ekstrakurikuler
     */
    public function saveEkskul(): void
    {
        Middleware::requireRole([ROLE_WALI_KELAS]);
        if (!isPost())
            redirect('?page=monitoring');

        $siswaId = (int) post('siswa_id');
        $semester = (int) post('semester', 1);
        $ekskulNames = post('ekskul_nama', []);
        $ekskulKets = post('ekskul_ket', []);

        $userId = Session::getUserId();
        $siswa = $this->siswaModel->findById($siswaId);
        if (!$siswa || !$this->checkWaliAccess((int) $siswa['kelas_id'], $userId)) {
            flashError('Akses ditolak atau siswa tidak ditemukan.');
            redirect('?page=monitoring');
        }

        $ekskulData = [];
        foreach ($ekskulNames as $i => $name) {
            if (!empty($name)) {
                $ekskulData[] = [
                    'nama' => $name,
                    'keterangan' => $ekskulKets[$i] ?? ''
                ];
            }
        }

        try {
            $this->nilaiModel->saveEkskul($siswaId, $semester, $ekskulData);
            flashSuccess('Data ekstrakurikuler berhasil disimpan.');
        } catch (Exception $e) {
            flashError('Gagal menyimpan ekskul: ' . $e->getMessage());
        }

        redirect('?page=monitoring&kelas_id=' . $siswa['kelas_id']);
    }

    private function checkWaliAccess(int $kelasId, int $userId): bool
    {
        $stmt = getDB()->prepare("SELECT id FROM wali_kelas WHERE kelas_id = ? AND user_id = ?");
        $stmt->execute([$kelasId, $userId]);
        return (bool) $stmt->fetch();
    }

    /**
     * Wali Kelas: Finalisasi kelas
     */
    public function finalisasi(): void
    {
        Middleware::requireRole([ROLE_WALI_KELAS]);

        if (!isPost())
            redirect('?page=monitoring');

        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token keamanan tidak valid.');
            redirect('?page=monitoring');
        }

        $userId = Session::getUserId();
        $kelasId = (int) post('kelas_id');
        $semester = (int) post('semester', 1);

        // Validasi kelas adalah wali kelas ini
        $kelasList = $this->kelasModel->getByWali($userId);
        $isWali = false;
        foreach ($kelasList as $k) {
            if ((int) $k['id'] === $kelasId) {
                $isWali = true;
                break;
            }
        }

        if (!$isWali) {
            flashError('Akses ditolak.');
            redirect('?page=monitoring');
        }

        // Cek semua mapel sudah lengkap
        $mapelStatus = $this->nilaiModel->getMapelStatusDiKelas($kelasId, $semester);
        $belumLengkap = 0;
        foreach ($mapelStatus as $ms) {
            if ($ms['status'] !== 'lengkap')
                $belumLengkap++;
        }

        if ($belumLengkap > 0) {
            flashError("Finalisasi gagal. Masih terdapat {$belumLengkap} mata pelajaran yang belum lengkap.");
            redirect('?page=monitoring&kelas_id=' . $kelasId . '&semester=' . $semester);
        }

        if (empty($mapelStatus)) {
            flashError('Tidak ada data pengampuan yang ditemukan untuk kelas ini.');
            redirect('?page=monitoring&kelas_id=' . $kelasId . '&semester=' . $semester);
        }

        // Update status kelas menjadi final
        $this->kelasModel->updateStatus($kelasId, STATUS_FINAL);

        // Notifikasi ke semua guru yang mengampu kelas ini
        $this->notifyAllGuruKelas($kelasId, $semester);

        // Notifikasi ke wali kelas sendiri
        $kelas = $this->kelasModel->findById($kelasId);
        $this->userModel->createNotification(
            $userId,
            "Finalisasi rapor kelas {$kelas['nama']} semester {$semester} berhasil dilakukan."
        );

        flashSuccess("Kelas {$kelas['nama']} berhasil difinalisasi. Semua nilai sekarang read-only.");
        redirect('?page=monitoring&kelas_id=' . $kelasId . '&semester=' . $semester);
    }

    /**
     * Kaprogli: Monitoring per jurusan
     */
    public function monitoringKaprogli(): void
    {
        Middleware::requireRole([ROLE_KAPROGLI]);
        $userId = Session::getUserId();

        $jurusanList = (new JurusanModel())->getByKaprogli($userId);
        $selectedJurusanId = (int) get_param('jurusan_id', 0);

        $kelasProgress = [];
        $stats = [];
        $jurusan = null;

        if ($selectedJurusanId > 0) {
            // Validasi jurusan adalah milik kaprogli ini
            $isKaprogli = false;
            foreach ($jurusanList as $j) {
                if ((int) $j['id'] === $selectedJurusanId) {
                    $isKaprogli = true;
                    $jurusan = $j;
                    break;
                }
            }

            if (!$isKaprogli) {
                flashError('Akses ditolak.');
                redirect('?page=monitoring_kaprogli');
            }

            $jModel = new JurusanModel();
            $stats = $jModel->getMonitoringStats($selectedJurusanId);
            $kelasProgress = $jModel->getKelasProgressByJurusan($selectedJurusanId);
        }

        renderView('nilai/monitoring_kaprogli', [
            'title' => 'Monitoring Jurusan – ' . APP_NAME,
            'jurusan_list' => $jurusanList,
            'selected_jurusan_id' => $selectedJurusanId,
            'jurusan' => $jurusan,
            'stats' => $stats,
            'kelas_progress' => $kelasProgress,
        ]);
    }

    private function notifyWaliKelas(int $kelasId, string $mapelNama): void
    {
        $wali = $this->kelasModel->getWali($kelasId);
        if ($wali) {
            $kelas = $this->kelasModel->findById($kelasId);
            $this->userModel->createNotification(
                (int) $wali['id'],
                "Nilai mata pelajaran {$mapelNama} untuk kelas {$kelas['nama']} telah diperbarui."
            );
        }
    }

    private function notifyAllGuruKelas(int $kelasId, int $semester): void
    {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT DISTINCT guru_id FROM pengampuan WHERE kelas_id = ? AND status = 'approved'"
        );
        $stmt->execute([$kelasId]);
        $guruIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $kelas = $this->kelasModel->findById($kelasId);
        foreach ($guruIds as $guruId) {
            $this->userModel->createNotification(
                (int) $guruId,
                "Rapor kelas {$kelas['nama']} semester {$semester} telah difinalisasi. Nilai tidak dapat diubah lagi."
            );
        }
    }
}
