-- ============================================================
-- e-Rapor Sisipan – SMKN 10 Surabaya
-- Database Schema – Comprehensive Version
-- ============================================================

SET NAMES utf8mb4;
SET foreign_key_checks = 0;

-- Buat database
DROP DATABASE IF EXISTS erapor10;
CREATE DATABASE erapor10 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE erapor10;

-- ============================================================
-- USERS & ROLES
-- ============================================================
CREATE TABLE users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(150) NOT NULL,
    email      VARCHAR(200) NOT NULL UNIQUE,
    nip        VARCHAR(50) NULL,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE roles (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO roles (name) VALUES
    ('admin'),
    ('guru_mapel'),
    ('kaprogli'),
    ('wali_kelas'),
    ('pembina_ekskul');

CREATE TABLE user_roles (
    user_id INT UNSIGNED NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- ACADEMIC STRUCTURE
-- ============================================================
CREATE TABLE jurusan (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE kaprogli_jurusan (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    jurusan_id INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED NOT NULL,
    UNIQUE KEY uk_kaprogli_jurusan (jurusan_id),
    FOREIGN KEY (jurusan_id) REFERENCES jurusan(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tahun_ajaran (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama      VARCHAR(50) NOT NULL UNIQUE,
    is_active TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO tahun_ajaran (nama, is_active) VALUES
    ('2023/2024 Ganjil', 0),
    ('2023/2024 Genap', 0),
    ('2024/2025 Ganjil', 1);

CREATE TABLE kelas (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama            VARCHAR(100) NOT NULL,
    jurusan_id      INT UNSIGNED NOT NULL,
    tingkat         INT NOT NULL,
    tahun_ajaran_id INT UNSIGNED NOT NULL,
    status          ENUM('proses','siap_final','final') DEFAULT 'proses',
    FOREIGN KEY (jurusan_id) REFERENCES jurusan(id) ON DELETE CASCADE,
    FOREIGN KEY (tahun_ajaran_id) REFERENCES tahun_ajaran(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE wali_kelas (
    id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kelas_id INT UNSIGNED NOT NULL,
    user_id  INT UNSIGNED NOT NULL,
    UNIQUE KEY uk_wali_kelas (kelas_id),
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE mapel (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama       VARCHAR(150) NOT NULL,
    jurusan_id INT UNSIGNED NOT NULL,
    kategori   ENUM('Muatan Nasional', 'Muatan Kewilayahan', 'Muatan Kejuruan') DEFAULT 'Muatan Nasional',
    kktp       INT UNSIGNED DEFAULT 75,
    FOREIGN KEY (jurusan_id) REFERENCES jurusan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- STUDENTS & ATTENDANCE
-- ============================================================
CREATE TABLE siswa (
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama     VARCHAR(150) NOT NULL,
    nis      VARCHAR(50) NULL,
    nisn     VARCHAR(50) NULL,
    kelas_id INT UNSIGNED NOT NULL,
    status   ENUM('aktif', 'lulus') DEFAULT 'aktif',
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE absensi_mapel (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pengampuan_id INT UNSIGNED NOT NULL,
    siswa_id      INT UNSIGNED NOT NULL,
    tanggal       DATE NOT NULL,
    semester      TINYINT UNSIGNED NOT NULL,
    status        ENUM('H','S','I','A') DEFAULT 'H',
    FOREIGN KEY (pengampuan_id) REFERENCES pengampuan(id) ON DELETE CASCADE,
    FOREIGN KEY (siswa_id)      REFERENCES siswa(id)      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- EXTRACURRICULAR
-- ============================================================
CREATE TABLE master_ekskul (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    pembina_nama VARCHAR(150) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ekskul_pembina (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ekskul_id  INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED NOT NULL,
    UNIQUE KEY uk_ekskul_pembina (ekskul_id),
    FOREIGN KEY (ekskul_id) REFERENCES master_ekskul(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)   REFERENCES users(id)         ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ekskul_nilai (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ekskul_id    INT UNSIGNED NOT NULL,
    siswa_id     INT UNSIGNED NOT NULL,
    semester     TINYINT UNSIGNED NOT NULL DEFAULT 1,
    tahun_ajaran VARCHAR(20) NOT NULL,
    nilai        ENUM('A', 'B', 'C', 'D') DEFAULT 'B',
    keterangan   TEXT,
    UNIQUE KEY uk_ekskul_nilai (ekskul_id, siswa_id, semester, tahun_ajaran),
    FOREIGN KEY (ekskul_id) REFERENCES master_ekskul(id) ON DELETE CASCADE,
    FOREIGN KEY (siswa_id)  REFERENCES siswa(id)         ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- GRADES & MONITORING
-- ============================================================
CREATE TABLE nilai (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    siswa_id        INT UNSIGNED NOT NULL,
    mapel_id        INT UNSIGNED NOT NULL,
    tahun_ajaran_id INT UNSIGNED NOT NULL,
    semester        TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=Ganjil, 2=Genap',
    
    -- Nilai Akademik
    s1              DECIMAL(5,2) DEFAULT NULL COMMENT 'Sumatif 1',
    s2              DECIMAL(5,2) DEFAULT NULL COMMENT 'Sumatif 2',
    s3              DECIMAL(5,2) DEFAULT NULL COMMENT 'Sumatif 3',
    pts             DECIMAL(5,2) DEFAULT NULL COMMENT 'PTS',
    
    -- Absensi Rekap per Mapel
    sakit           INT UNSIGNED DEFAULT 0,
    izin            INT UNSIGNED DEFAULT 0,
    alfa            INT UNSIGNED DEFAULT 0,
    
    status          ENUM('draft','lengkap') DEFAULT 'draft',
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_nilai (siswa_id, mapel_id, tahun_ajaran_id, semester),
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
    FOREIGN KEY (mapel_id) REFERENCES mapel(id) ON DELETE CASCADE,
    FOREIGN KEY (tahun_ajaran_id) REFERENCES tahun_ajaran(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE catatan_wali (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    siswa_id   INT UNSIGNED NOT NULL,
    semester   TINYINT UNSIGNED NOT NULL,
    sikap      TEXT,
    catatan    TEXT,
    UNIQUE KEY uk_catatan (siswa_id, semester),
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SAMPLE DATA
-- ============================================================

-- Users (Password123)
INSERT INTO users (name, email, password) VALUES
    ('Administrator',  'admin@smkn10sby.sch.id',      '$2y$10$Akq7G5hGeZEL1eOPcVdYTOluWxGG90rsIw3ao7fGvqt4WYpDkjO/C'),
    ('Budi Santoso',   'budi@smkn10sby.sch.id',       '$2y$10$Akq7G5hGeZEL1eOPcVdYTOluWxGG90rsIw3ao7fGvqt4WYpDkjO/C'),
    ('Dewi Rahayu',    'dewi@smkn10sby.sch.id',       '$2y$10$Akq7G5hGeZEL1eOPcVdYTOluWxGG90rsIw3ao7fGvqt4WYpDkjO/C'),
    ('Sari Wulandari', 'sari@smkn10sby.sch.id',       '$2y$10$Akq7G5hGeZEL1eOPcVdYTOluWxGG90rsIw3ao7fGvqt4WYpDkjO/C'),
    ('Ahmad Fauzi',    'ahmad@smkn10sby.sch.id',      '$2y$10$Akq7G5hGeZEL1eOPcVdYTOluWxGG90rsIw3ao7fGvqt4WYpDkjO/C'),
    ('Rina Kusuma',    'rina@smkn10sby.sch.id',       '$2y$10$Akq7G5hGeZEL1eOPcVdYTOluWxGG90rsIw3ao7fGvqt4WYpDkjO/C'),
    ('Dian Pratiwi',   'dian@smkn10sby.sch.id',       '$2y$10$Akq7G5hGeZEL1eOPcVdYTOluWxGG90rsIw3ao7fGvqt4WYpDkjO/C'),
    ('Hendra Wijaya',  'hendra@smkn10sby.sch.id',     '$2y$10$Akq7G5hGeZEL1eOPcVdYTOluWxGG90rsIw3ao7fGvqt4WYpDkjO/C'),
    ('Coach John',     'ekskul@smkn10sby.sch.id',     '$2y$10$Akq7G5hGeZEL1eOPcVdYTOluWxGG90rsIw3ao7fGvqt4WYpDkjO/C');

-- Roles assignment
INSERT INTO user_roles (user_id, role_id) VALUES (1, 1);
INSERT INTO user_roles (user_id, role_id) VALUES (2, 2), (2, 4);
INSERT INTO user_roles (user_id, role_id) VALUES (3, 2);
INSERT INTO user_roles (user_id, role_id) VALUES (4, 2);
INSERT INTO user_roles (user_id, role_id) VALUES (5, 3);
INSERT INTO user_roles (user_id, role_id) VALUES (6, 3);
INSERT INTO user_roles (user_id, role_id) VALUES (7, 4);
INSERT INTO user_roles (user_id, role_id) VALUES (8, 4);
INSERT INTO user_roles (user_id, role_id) VALUES (9, 5);

INSERT INTO jurusan (nama) VALUES
    ('Teknik Komputer dan Jaringan'),
    ('Rekayasa Perangkat Lunak'),
    ('Akuntansi');

INSERT INTO kelas (nama, jurusan_id, tingkat, tahun_ajaran_id, status) VALUES
    ('XI TKJ 1', 1, 11, 3, 'proses'),
    ('XI TKJ 2', 1, 11, 3, 'proses'),
    ('XI RPL 1', 2, 11, 3, 'proses');

INSERT INTO mapel (nama, jurusan_id, kategori) VALUES
    ('Matematika', 1, 'Muatan Nasional'),
    ('Bahasa Indonesia', 1, 'Muatan Nasional'),
    ('Networking', 1, 'Muatan Kejuruan');

INSERT INTO siswa (nama, kelas_id, nis) VALUES
    ('Andi Prasetyo', 1, '1001'),
    ('Bella Oktaviani', 1, '1002'),
    ('Ivan Setiawan', 2, '2001');

INSERT INTO master_ekskul (nama, pembina_nama) VALUES
    ('Pramuka', 'John Doe'),
    ('Basket', 'Coach John');

INSERT INTO ekskul_pembina (ekskul_id, user_id) VALUES (2, 9);

SET foreign_key_checks = 1;
