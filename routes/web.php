<?php
/**
 * Routing Definitions
 * Format: page => [controller, action, roles_required]
 */

return [
    // Landing page
    'landing' => ['LandingController', 'index', []],
    'api.stats' => ['LandingController', 'stats', []],

    // Auth
    'login' => ['AuthController', 'login', []],
    'logout' => ['AuthController', 'logout', []],

    // Dashboard
    'dashboard' => ['DashboardController', 'index', []],

    // Nilai – Guru
    'nilai' => ['NilaiController', 'inputForm', [ROLE_GURU_MAPEL]],
    'nilai.save' => ['NilaiController', 'saveNilai', [ROLE_GURU_MAPEL]],
    'presensi' => ['PresensiController', 'index', [ROLE_GURU_MAPEL]],
    'presensi.input' => ['PresensiController', 'input', [ROLE_GURU_MAPEL]],
    'presensi.save' => ['PresensiController', 'save', [ROLE_GURU_MAPEL]],

    // Monitoring – Wali Kelas
    'monitoring' => ['NilaiController', 'monitoring', [ROLE_WALI_KELAS]],
    'monitoring.finalisasi' => ['NilaiController', 'finalisasi', [ROLE_WALI_KELAS]],
    'monitoring.save_catatan' => ['NilaiController', 'saveCatatan', [ROLE_WALI_KELAS]],
    'monitoring.save_tanggal' => ['NilaiController', 'saveTanggalRapor', [ROLE_WALI_KELAS]],
    'rapor.print' => ['NilaiController', 'printRapor', [ROLE_WALI_KELAS]],
    
    // Ekstrakurikuler – Pembina
    'ekskul.anggota' => ['EkskulController', 'indexAnggota', [ROLE_PEMBINA_EKSKUL, ROLE_ADMIN]],
    'ekskul.nilai' => ['EkskulController', 'indexNilai', [ROLE_PEMBINA_EKSKUL, ROLE_ADMIN]],
    'ekskul.input' => ['EkskulController', 'inputForm', [ROLE_PEMBINA_EKSKUL, ROLE_ADMIN]],
    'ekskul.save' => ['EkskulController', 'save', [ROLE_PEMBINA_EKSKUL, ROLE_ADMIN]],
    'ekskul.members' => ['EkskulController', 'members', [ROLE_PEMBINA_EKSKUL, ROLE_ADMIN]],
    'ekskul.add_member' => ['EkskulController', 'addMember', [ROLE_PEMBINA_EKSKUL, ROLE_ADMIN]],
    'ekskul.remove_member' => ['EkskulController', 'removeMember', [ROLE_PEMBINA_EKSKUL, ROLE_ADMIN]],

    // Monitoring – Kaprogli
    'monitoring_kaprogli' => ['NilaiController', 'monitoringKaprogli', [ROLE_KAPROGLI]],

    // Admin – Users
    'admin.users' => ['UserController', 'index', [ROLE_ADMIN]],
    'admin.users.create' => ['UserController', 'create', [ROLE_ADMIN]],
    'admin.users.update' => ['UserController', 'update', [ROLE_ADMIN]],
    'admin.users.delete' => ['UserController', 'delete', [ROLE_ADMIN]],

    // Admin – Jurusan
    'admin.jurusan' => ['AcademicController', 'jurusan', [ROLE_ADMIN]],
    'admin.jurusan.create' => ['AcademicController', 'createJurusan', [ROLE_ADMIN]],
    'admin.jurusan.update' => ['AcademicController', 'updateJurusan', [ROLE_ADMIN]],
    'admin.jurusan.delete' => ['AcademicController', 'deleteJurusan', [ROLE_ADMIN]],

    // Admin – Kelas
    'admin.kelas' => ['AcademicController', 'kelas', [ROLE_ADMIN]],
    'admin.kelas.create' => ['AcademicController', 'createKelas', [ROLE_ADMIN]],
    'admin.kelas.update' => ['AcademicController', 'updateKelas', [ROLE_ADMIN]],
    'admin.kelas.delete' => ['AcademicController', 'deleteKelas', [ROLE_ADMIN]],

    // Admin – Mapel
    'admin.mapel' => ['AcademicController', 'mapel', [ROLE_ADMIN]],
    'admin.mapel.create' => ['AcademicController', 'createMapel', [ROLE_ADMIN]],
    'admin.mapel.update' => ['AcademicController', 'updateMapel', [ROLE_ADMIN]],
    'admin.mapel.delete' => ['AcademicController', 'deleteMapel', [ROLE_ADMIN]],

    // Admin – Siswa
    'admin.siswa' => ['AcademicController', 'siswa', [ROLE_ADMIN]],
    'admin.siswa.create' => ['AcademicController', 'createSiswa', [ROLE_ADMIN]],
    'admin.siswa.update' => ['AcademicController', 'updateSiswa', [ROLE_ADMIN]],
    'admin.siswa.delete' => ['AcademicController', 'deleteSiswa', [ROLE_ADMIN]],
    'admin.siswa.import' => ['AcademicController', 'importSiswa', [ROLE_ADMIN]],

    // Admin – Pengampuan
    'admin.pengampuan' => ['AcademicController', 'pengampuan', [ROLE_ADMIN]],
    'admin.pengampuan.create' => ['AcademicController', 'createPengampuan', [ROLE_ADMIN]],
    'admin.pengampuan.update' => ['AcademicController', 'updatePengampuan', [ROLE_ADMIN]],
    'admin.pengampuan.delete' => ['AcademicController', 'deletePengampuan', [ROLE_ADMIN]],

    // Admin – Ekskul
    'admin.ekskul' => ['AcademicController', 'ekskul', [ROLE_ADMIN]],
    'admin.ekskul.create' => ['AcademicController', 'createEkskul', [ROLE_ADMIN]],
    'admin.ekskul.update' => ['AcademicController', 'updateEkskul', [ROLE_ADMIN]],
    'admin.ekskul.delete' => ['AcademicController', 'deleteEkskul', [ROLE_ADMIN]],
    'admin.ekskul.input' => ['AcademicController', 'inputEkskul', [ROLE_ADMIN]],
    'admin.ekskul.save' => ['AcademicController', 'saveEkskul', [ROLE_ADMIN]],
    'admin.ekskul.members' => ['AcademicController', 'membersEkskul', [ROLE_ADMIN]],
    'admin.ekskul.add_member' => ['AcademicController', 'addMemberEkskul', [ROLE_ADMIN]],
    'admin.ekskul.remove_member' => ['AcademicController', 'removeMemberEkskul', [ROLE_ADMIN]],


    // Admin – Tahun Ajaran Baru & Reset
    'admin.tahun_ajaran_baru' => ['AdminController', 'tahunAjaranBaru', [ROLE_ADMIN]],
    'admin.tahun_ajaran_baru.process' => ['AdminController', 'processTahunAjaranBaru', [ROLE_ADMIN]],
    'admin.tahun_ajaran_baru.reset' => ['AdminController', 'resetTahunAjaran', [ROLE_ADMIN]],
    'save-date' => ['AdminController', 'updateTanggalRapor', [ROLE_ADMIN]],
];






