<?php

define('APP_NAME', 'e-Rapor Sisipan');
define('APP_VERSION', '1.0.0');
define('SCHOOL_NAME', 'SMKN 10 Surabaya');
define('BASE_URL', 'http://localhost/erapor');
// Paths are now defined in bootstrap/app.php


// Session config
define('SESSION_NAME', 'erapor10_session');
define('SESSION_LIFETIME', 7200); // 2 jam

// Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_GURU_MAPEL', 'guru_mapel');
define('ROLE_KAPROGLI', 'kaprogli');
define('ROLE_WALI_KELAS', 'wali_kelas');
define('ROLE_PEMBINA_EKSKUL', 'pembina_ekskul');

// Kelas Status
define('STATUS_PROSES', 'proses');
define('STATUS_SIAP_FINAL', 'siap_final');
define('STATUS_FINAL', 'final');

// Nilai Status
define('NILAI_DRAFT', 'draft');
define('NILAI_LENGKAP', 'lengkap');

// Semester
define('SEMESTER_GANJIL', 1);
define('SEMESTER_GENAP', 2);
