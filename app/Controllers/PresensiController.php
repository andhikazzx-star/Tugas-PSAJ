<?php

class PresensiController
{
    private PresensiModel $presensiModel;
    private PengampuanModel $pengampuanModel;

    public function __construct()
    {
        $this->presensiModel = new PresensiModel();
        $this->pengampuanModel = new PengampuanModel();
    }

    /**
     * List sessions/dates for a mapel
     */
    public function index(): void
    {
        Middleware::requireRole([ROLE_GURU_MAPEL]);
        $userId = Session::getUserId();
        
        $pengampuanId = (int) get_param('pengampuan_id');
        $semester = (int) get_param('semester', 1);

        $pengampuan = $this->pengampuanModel->findById($pengampuanId);
        if (!$pengampuan || (int) $pengampuan['guru_id'] !== $userId) {
            flashError('Akses ditolak.');
            redirect('?page=nilai');
        }

        // Get existing attendance dates
        $stmt = getDB()->prepare(
            "SELECT tanggal, COUNT(DISTINCT siswa_id) as total_siswa 
             FROM absensi_mapel 
             WHERE pengampuan_id = ? AND semester = ?
             GROUP BY tanggal ORDER BY tanggal DESC"
        );
        $stmt->execute([$pengampuanId, $semester]);
        $dates = $stmt->fetchAll();

        renderView('nilai/presensi_list', [
            'title' => 'Daftar Presensi – ' . $pengampuan['mapel_nama'],
            'pengampuan' => $pengampuan,
            'semester' => $semester,
            'dates' => $dates
        ]);
    }

    /**
     * Input/Edit Attendance for a date
     */
    public function input(): void
    {
        Middleware::requireRole([ROLE_GURU_MAPEL]);
        $userId = Session::getUserId();
        
        $pengampuanId = (int) get_param('pengampuan_id');
        $semester = (int) get_param('semester', 1);
        $date = get_param('date', date('Y-m-d'));

        $pengampuan = $this->pengampuanModel->findById($pengampuanId);
        if (!$pengampuan || (int) $pengampuan['guru_id'] !== $userId) {
            flashError('Akses ditolak.');
            redirect('?page=nilai');
        }

        $attendance = $this->presensiModel->getAttendance($pengampuanId, $date);

        renderView('nilai/presensi_input', [
            'title' => 'Input Presensi – ' . $date,
            'pengampuan' => $pengampuan,
            'semester' => $semester,
            'date' => $date,
            'attendance' => $attendance
        ]);
    }

    /**
     * Save Attendance (POST)
     */
    public function save(): void
    {
        Middleware::requireRole([ROLE_GURU_MAPEL]);
        if (!isPost()) redirect('?page=nilai');

        $userId = Session::getUserId();
        $pengampuanId = (int) post('pengampuan_id');
        $semester = (int) post('semester');
        $date = post('date');
        $data = post('presensi', []);

        $pengampuan = $this->pengampuanModel->findById($pengampuanId);
        if (!$pengampuan || (int) $pengampuan['guru_id'] !== $userId) {
            flashError('Akses ditolak.');
            redirect('?page=nilai');
        }

        if ($this->presensiModel->saveAttendance($pengampuanId, $semester, $date, $data)) {
            flashSuccess('Presensi tanggal ' . $date . ' berhasil disimpan.');
        } else {
            flashError('Gagal menyimpan presensi.');
        }

        redirect('?page=presensi&pengampuan_id=' . $pengampuanId . '&semester=' . $semester);
    }
}
