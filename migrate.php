<?php
/**
 * Migration Script - e-Rapor Sisipan
 * Jalankan file ini melalui browser (misal: http://localhost/Tugas-PSAJ/migrate.php)
 * untuk memperbarui struktur database Anda secara otomatis.
 */

require_once __DIR__ . '/bootstrap/app.php';

echo "<h1>Database Migration Tools</h1>";
echo "<p>Sedang memperbarui struktur tabel <code>nilai</code>...</p>";

$db = getDB();

try {
    // 1. Tambahkan kolom baru ke tabel nilai jika belum ada
    $columnsToAdd = [
        's1' => "DECIMAL(5,2) DEFAULT NULL AFTER semester",
        's2' => "DECIMAL(5,2) DEFAULT NULL AFTER s1",
        's3' => "DECIMAL(5,2) DEFAULT NULL AFTER s2",
        'pts' => "DECIMAL(5,2) DEFAULT NULL AFTER s3",
        'sakit' => "INT UNSIGNED DEFAULT 0 AFTER pts",
        'izin' => "INT UNSIGNED DEFAULT 0 AFTER sakit",
        'alfa' => "INT UNSIGNED DEFAULT 0 AFTER izin",
        'tahun_ajaran_id' => "INT UNSIGNED NOT NULL AFTER mapel_id"
    ];

    foreach ($columnsToAdd as $col => $definition) {
        $check = $db->query("SHOW COLUMNS FROM nilai LIKE '$col'")->fetch();
        if (!$check) {
            $db->exec("ALTER TABLE nilai ADD COLUMN $col $definition");
            echo "✅ Kolom <code>$col</code> berhasil ditambahkan.<br>";
        } else {
            echo "ℹ️ Kolom <code>$col</code> sudah ada.<br>";
        }
    }

    // 2. Tambahkan kolom ke tabel mapel jika belum ada
    $mapelCols = [
        'kategori' => "ENUM('Muatan Nasional', 'Muatan Kewilayahan', 'Muatan Kejuruan') DEFAULT 'Muatan Nasional' AFTER jurusan_id",
        'kktp' => "INT UNSIGNED DEFAULT 75 AFTER kategori"
    ];
    foreach ($mapelCols as $col => $definition) {
        $check = $db->query("SHOW COLUMNS FROM mapel LIKE '$col'")->fetch();
        if (!$check) {
            $db->exec("ALTER TABLE mapel ADD COLUMN $col $definition");
            echo "✅ Kolom <code>$col</code> berhasil ditambahkan ke tabel mapel.<br>";
        }
    }

    // 3. Buat tabel catatan_wali jika belum ada
    $db->exec("CREATE TABLE IF NOT EXISTS catatan_wali (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        siswa_id INT UNSIGNED NOT NULL,
        semester TINYINT UNSIGNED NOT NULL,
        sikap TEXT,
        catatan TEXT,
        UNIQUE KEY uk_catatan (siswa_id, semester),
        FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "✅ Tabel <code>catatan_wali</code> siap.<br>";

    // 4. Buat tabel absensi_mapel jika belum ada
    $db->exec("CREATE TABLE IF NOT EXISTS absensi_mapel (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        pengampuan_id INT UNSIGNED NOT NULL,
        siswa_id INT UNSIGNED NOT NULL,
        tanggal DATE NOT NULL,
        semester TINYINT UNSIGNED NOT NULL,
        status ENUM('H','S','I','A') DEFAULT 'H',
        FOREIGN KEY (pengampuan_id) REFERENCES pengampuan(id) ON DELETE CASCADE,
        FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "✅ Tabel <code>absensi_mapel</code> siap.<br>";


    echo "<h2>Migrasi Berhasil Selesai!</h2>";
    echo "<p>Silakan kembali ke <a href='index.php'>Halaman Utama</a>.</p>";
    echo "<p><strong>PENTING:</strong> Segera hapus file <code>migrate.php</code> ini demi keamanan.</p>";

} catch (PDOException $e) {
    echo "<h2 style='color:red;'>Terjadi Kesalahan:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<p>Pastikan Anda memiliki hak akses yang cukup pada database.</p>";
}
