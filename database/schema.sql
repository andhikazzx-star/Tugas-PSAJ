-- ============================================================
-- e-Rapor Sisipan – SMKN 10 Surabaya
-- Database Schema + Sample Data
-- ============================================================

SET NAMES utf8mb4;
SET foreign_key_checks = 0;

-- Buat database (jalankan manual jika belum ada)
DROP DATABASE IF EXISTS erapor10;
CREATE DATABASE erapor10 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE erapor10;

-- ============================================================
-- TABEL USERS
-- ============================================================
CREATE TABLE users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(150) NOT NULL,
    email      VARCHAR(200) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL ROLES
-- ============================================================
CREATE TABLE roles (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO roles (name) VALUES
    ('admin'),
    ('guru_mapel'),
    ('kaprogli'),
    ('wali_kelas');

-- ============================================================
-- TABEL USER_ROLES (MANY-TO-MANY)
-- ============================================================
CREATE TABLE user_roles (
    user_id INT UNSIGNED NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL JURUSAN
-- ============================================================
CREATE TABLE jurusan (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL KAPROGLI_JURUSAN (mapping kaprogli ke jurusan)
-- ============================================================
CREATE TABLE kaprogli_jurusan (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    jurusan_id INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED NOT NULL,
    UNIQUE KEY uk_kaprogli_jurusan (jurusan_id),
    FOREIGN KEY (jurusan_id) REFERENCES jurusan(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL KELAS
-- ============================================================
CREATE TABLE kelas (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama         VARCHAR(100) NOT NULL,
    jurusan_id   INT UNSIGNED NOT NULL,
    tahun_ajaran VARCHAR(20)  NOT NULL,
    status       ENUM('proses','siap_final','final') DEFAULT 'proses',
    FOREIGN KEY (jurusan_id) REFERENCES jurusan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL WALI_KELAS (mapping wali ke kelas)
-- ============================================================
CREATE TABLE wali_kelas (
    id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kelas_id INT UNSIGNED NOT NULL,
    user_id  INT UNSIGNED NOT NULL,
    UNIQUE KEY uk_wali_kelas (kelas_id),
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL MAPEL
-- ============================================================
CREATE TABLE mapel (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama       VARCHAR(150) NOT NULL,
    jurusan_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (jurusan_id) REFERENCES jurusan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL PENGAMPUAN
-- ============================================================
CREATE TABLE pengampuan (
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    guru_id  INT UNSIGNED NOT NULL,
    mapel_id INT UNSIGNED NOT NULL,
    kelas_id INT UNSIGNED NOT NULL,
    status   ENUM('pending','approved') DEFAULT 'approved',
    UNIQUE KEY uk_pengampuan (guru_id, mapel_id, kelas_id),
    FOREIGN KEY (guru_id)  REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (mapel_id) REFERENCES mapel(id)  ON DELETE CASCADE,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL SISWA
-- ============================================================
CREATE TABLE siswa (
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama     VARCHAR(150) NOT NULL,
    kelas_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL NILAI
-- ============================================================
CREATE TABLE nilai (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    siswa_id   INT UNSIGNED NOT NULL,
    mapel_id   INT UNSIGNED NOT NULL,
    semester   TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=Ganjil, 2=Genap',
    nilai      DECIMAL(5,2) DEFAULT NULL,
    status     ENUM('draft','lengkap') DEFAULT 'draft',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_nilai (siswa_id, mapel_id, semester),
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
    FOREIGN KEY (mapel_id) REFERENCES mapel(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL NOTIFICATIONS
-- ============================================================
CREATE TABLE notifications (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    message    TEXT NOT NULL,
    is_read    TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SAMPLE DATA
-- ============================================================

-- Users (password: Password123 untuk semua akun)
-- Hash: password_hash('Password123', PASSWORD_BCRYPT)
INSERT INTO users (name, email, password) VALUES
    ('Administrator',  'admin@smkn10sby.sch.id',      '$2y$12$xLfyJjRnLBV3bx.sNjW7bOGJQ6bTyZqg5/W7L4MHVPE5HtHBHiZ5y'),
    ('Budi Santoso',   'budi@smkn10sby.sch.id',       '$2y$12$xLfyJjRnLBV3bx.sNjW7bOGJQ6bTyZqg5/W7L4MHVPE5HtHBHiZ5y'),
    ('Dewi Rahayu',    'dewi@smkn10sby.sch.id',       '$2y$12$xLfyJjRnLBV3bx.sNjW7bOGJQ6bTyZqg5/W7L4MHVPE5HtHBHiZ5y'),
    ('Sari Wulandari', 'sari@smkn10sby.sch.id',       '$2y$12$xLfyJjRnLBV3bx.sNjW7bOGJQ6bTyZqg5/W7L4MHVPE5HtHBHiZ5y'),
    ('Ahmad Fauzi',    'ahmad@smkn10sby.sch.id',      '$2y$12$xLfyJjRnLBV3bx.sNjW7bOGJQ6bTyZqg5/W7L4MHVPE5HtHBHiZ5y'),
    ('Rina Kusuma',    'rina@smkn10sby.sch.id',       '$2y$12$xLfyJjRnLBV3bx.sNjW7bOGJQ6bTyZqg5/W7L4MHVPE5HtHBHiZ5y'),
    ('Dian Pratiwi',   'dian@smkn10sby.sch.id',       '$2y$12$xLfyJjRnLBV3bx.sNjW7bOGJQ6bTyZqg5/W7L4MHVPE5HtHBHiZ5y'),
    ('Hendra Wijaya',  'hendra@smkn10sby.sch.id',     '$2y$12$xLfyJjRnLBV3bx.sNjW7bOGJQ6bTyZqg5/W7L4MHVPE5HtHBHiZ5y');

-- Assign Roles
-- admin role (role id = 1)
INSERT INTO user_roles (user_id, role_id) VALUES (1, 1); -- Admin
-- guru_mapel (role id = 2)
INSERT INTO user_roles (user_id, role_id) VALUES (2, 2); -- Budi = guru
INSERT INTO user_roles (user_id, role_id) VALUES (3, 2); -- Dewi = guru
INSERT INTO user_roles (user_id, role_id) VALUES (4, 2); -- Sari = guru
-- kaprogli (role id = 3)
INSERT INTO user_roles (user_id, role_id) VALUES (5, 3); -- Ahmad = kaprogli
INSERT INTO user_roles (user_id, role_id) VALUES (6, 3); -- Rina  = kaprogli
-- wali_kelas (role id = 4)
INSERT INTO user_roles (user_id, role_id) VALUES (7, 4); -- Dian  = wali
INSERT INTO user_roles (user_id, role_id) VALUES (8, 4); -- Hendra= wali
-- Budi juga wali kelas (multi-role)
INSERT INTO user_roles (user_id, role_id) VALUES (2, 4);

-- Jurusan
INSERT INTO jurusan (nama) VALUES
    ('Teknik Komputer dan Jaringan'),
    ('Rekayasa Perangkat Lunak'),
    ('Akuntansi dan Keuangan');

-- Kaprogli mapping
INSERT INTO kaprogli_jurusan (jurusan_id, user_id) VALUES (1, 5); -- Ahmad -> TKJ
INSERT INTO kaprogli_jurusan (jurusan_id, user_id) VALUES (2, 6); -- Rina  -> RPL

-- Kelas
INSERT INTO kelas (nama, jurusan_id, tahun_ajaran, status) VALUES
    ('XI TKJ 1', 1, '2024/2025', 'proses'),
    ('XI TKJ 2', 1, '2024/2025', 'proses'),
    ('XI RPL 1', 2, '2024/2025', 'proses'),
    ('XI AK 1',  3, '2024/2025', 'proses');

-- Wali Kelas mapping
INSERT INTO wali_kelas (kelas_id, user_id) VALUES (1, 7);  -- Dian -> XI TKJ 1
INSERT INTO wali_kelas (kelas_id, user_id) VALUES (2, 8);  -- Hendra -> XI TKJ 2
INSERT INTO wali_kelas (kelas_id, user_id) VALUES (3, 2);  -- Budi -> XI RPL 1

-- Mapel
INSERT INTO mapel (nama, jurusan_id) VALUES
    ('Matematika',          1),
    ('Bahasa Indonesia',    1),
    ('Jaringan Komputer',   1),
    ('Sistem Operasi',      1),
    ('Matematika',          2),
    ('Pemrograman Web',     2),
    ('Basis Data',          2),
    ('Matematika',          3),
    ('Akuntansi Dasar',     3);

-- Pengampuan
INSERT INTO pengampuan (guru_id, mapel_id, kelas_id, status) VALUES
    (2, 1, 1, 'approved'), -- Budi: Matematika -> XI TKJ 1
    (2, 3, 1, 'approved'), -- Budi: Jaringan Komputer -> XI TKJ 1
    (3, 2, 1, 'approved'), -- Dewi: Bahasa Indonesia -> XI TKJ 1
    (4, 4, 1, 'approved'), -- Sari: Sistem Operasi -> XI TKJ 1
    (2, 1, 2, 'approved'), -- Budi: Matematika -> XI TKJ 2
    (3, 2, 2, 'approved'), -- Dewi: B.Indonesia -> XI TKJ 2
    (4, 5, 3, 'approved'), -- Sari: Matematika -> XI RPL 1
    (3, 6, 3, 'approved'); -- Dewi: Pemrograman Web -> XI RPL 1

-- Siswa (XI TKJ 1)
INSERT INTO siswa (nama, kelas_id) VALUES
    ('Andi Prasetyo',     1),
    ('Bella Oktaviani',   1),
    ('Candra Wirawan',    1),
    ('Dina Fitriani',     1),
    ('Eko Nugroho',       1),
    ('Farida Hanum',      1),
    ('Gilang Ramadhan',   1),
    ('Hesty Permata',     1);

-- Siswa (XI TKJ 2)
INSERT INTO siswa (nama, kelas_id) VALUES
    ('Ivan Setiawan',     2),
    ('Joko Susanto',      2),
    ('Kiki Andriani',     2),
    ('Laila Maharani',    2),
    ('Mustofa Hadi',      2),
    ('Nita Puspita',      2);

-- Siswa (XI RPL 1)
INSERT INTO siswa (nama, kelas_id) VALUES
    ('Oky Pratama',       3),
    ('Putri Handayani',   3),
    ('Qori Nurhayati',    3),
    ('Rizky Maulana',     3),
    ('Sinta Dewi',        3),
    ('Tomi Kurniawan',    3);

SET foreign_key_checks = 1;

-- ============================================================
-- PASSWORD INFO
-- Semua akun sample menggunakan password: Password123
-- Untuk generate hash baru: password_hash('Password123', PASSWORD_BCRYPT, ['cost'=>12])
-- ============================================================
