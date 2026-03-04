<?php
/**
 * Routing System – Front Controller Pattern
 * URL: ?page=xxx&action=xxx
 */

$page = get_param('page', 'landing');
$action = get_param('action', 'index');

// Definisi routes: page => [controller, action, roles_required]
$routes = [
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

    // Monitoring – Wali Kelas
    'monitoring' => ['NilaiController', 'monitoring', [ROLE_WALI_KELAS]],
    'monitoring.finalisasi' => ['NilaiController', 'finalisasi', [ROLE_WALI_KELAS]],
    'monitoring.save_catatan' => ['NilaiController', 'saveCatatan', [ROLE_WALI_KELAS]],
    'monitoring.save_ekskul' => ['NilaiController', 'saveEkskul', [ROLE_WALI_KELAS]],
    'rapor.print' => ['NilaiController', 'printRapor', [ROLE_WALI_KELAS]],

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

    // Admin – Tahun Ajaran Baru & Reset
    'admin.tahun_ajaran_baru' => ['AdminController', 'tahunAjaranBaru', [ROLE_ADMIN]],
    'admin.tahun_ajaran_baru.process' => ['AdminController', 'processTahunAjaranBaru', [ROLE_ADMIN]],
    'admin.tahun_ajaran_baru.reset' => ['AdminController', 'resetTahunAjaran', [ROLE_ADMIN]],
];

// Dispatch route
if (isset($routes[$page])) {
    [$controllerName, $method, $roles] = $routes[$page];

    // Load controller (check /app/Controllers/ dir)
    $controllerFile = CONTROLLERS_PATH . DS . $controllerName . '.php';
    if (!file_exists($controllerFile)) {
        http_response_code(404);
        renderView('errors/404');
        exit;
    }

    require_once $controllerFile;
    $controller = new $controllerName();

    if (!method_exists($controller, $method)) {
        http_response_code(404);
        renderView('errors/404');
        exit;
    }

    $controller->$method();
} else {
    http_response_code(404);
    renderView('errors/404');
}
