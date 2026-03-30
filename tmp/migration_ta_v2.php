<?php
require_once __DIR__ . '/../bootstrap/app.php';
$db = getDB();

function dropIndexIfExists($db, $table, $index) {
    try {
        $db->exec("ALTER TABLE $table DROP INDEX $index");
        echo "Dropped index $index from $table\n";
    } catch (Exception $e) {
        echo "Note: Could not drop index $index from $table (might not exist)\n";
    }
}

try {
    echo "Starting migration...\n";

    // 1. Migrate catatan_wali
    echo "Updating catatan_wali table structure...\n";
    $cols = $db->query("DESCRIBE catatan_wali")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('tahun_ajaran_id', $cols)) {
        $db->exec("ALTER TABLE catatan_wali ADD COLUMN tahun_ajaran_id INT UNSIGNED AFTER semester");
        
        $stmtActive = $db->query("SELECT id FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
        $activeYear = $stmtActive->fetch();
        if ($activeYear) {
            $db->exec("UPDATE catatan_wali SET tahun_ajaran_id = " . $activeYear['id']);
        } else {
            // Need a fallback if no active year exists
            $firstYear = $db->query("SELECT id FROM tahun_ajaran LIMIT 1")->fetch();
            if ($firstYear) {
                $db->exec("UPDATE catatan_wali SET tahun_ajaran_id = " . $firstYear['id']);
            }
        }
        $db->exec("ALTER TABLE catatan_wali MODIFY COLUMN tahun_ajaran_id INT UNSIGNED NOT NULL");
    }

    dropIndexIfExists($db, 'catatan_wali', 'uk_catatan');
    $db->exec("ALTER TABLE catatan_wali ADD UNIQUE KEY uk_catatan (siswa_id, tahun_ajaran_id, semester)");
    
    // Add Foreign Key (ignore if exists)
    try {
        $db->exec("ALTER TABLE catatan_wali ADD FOREIGN KEY (tahun_ajaran_id) REFERENCES tahun_ajaran(id) ON DELETE CASCADE");
    } catch(Exception $e) {}

    // 2. Migrate ekskul_nilai
    echo "Updating ekskul_nilai table structure...\n";
    $cols = $db->query("DESCRIBE ekskul_nilai")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('tahun_ajaran_id', $cols)) {
        $db->exec("ALTER TABLE ekskul_nilai ADD COLUMN tahun_ajaran_id INT UNSIGNED AFTER semester");
        
        $years = $db->query("SELECT id, nama FROM tahun_ajaran")->fetchAll();
        foreach ($years as $y) {
            $stmt = $db->prepare("UPDATE ekskul_nilai SET tahun_ajaran_id = ? WHERE tahun_ajaran = ?");
            $stmt->execute([$y['id'], $y['nama']]);
        }
        
        $stmtActive = $db->query("SELECT id FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
        $activeYear = $stmtActive->fetch();
        if ($activeYear) {
            $db->exec("UPDATE ekskul_nilai SET tahun_ajaran_id = " . $activeYear['id'] . " WHERE tahun_ajaran_id IS NULL");
        }
        
        $db->exec("ALTER TABLE ekskul_nilai MODIFY COLUMN tahun_ajaran_id INT UNSIGNED NOT NULL");
    }

    dropIndexIfExists($db, 'ekskul_nilai', 'uk_ekskul_nilai');
    $db->exec("ALTER TABLE ekskul_nilai ADD UNIQUE KEY uk_ekskul_nilai (ekskul_id, siswa_id, semester, tahun_ajaran_id)");
    
    try {
        $db->exec("ALTER TABLE ekskul_nilai ADD FOREIGN KEY (tahun_ajaran_id) REFERENCES tahun_ajaran(id) ON DELETE CASCADE");
    } catch(Exception $e) {}

    if (in_array('tahun_ajaran', $cols)) {
        $db->exec("ALTER TABLE ekskul_nilai DROP COLUMN tahun_ajaran");
    }

    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration CRITICAL ERROR: " . $e->getMessage() . "\n";
}
