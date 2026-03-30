<?php
require_once __DIR__ . '/../bootstrap/app.php';
$db = getDB();

try {
    $db->beginTransaction();

    echo "Migrating catatan_wali...\n";
    // 1. Add tahun_ajaran_id to catatan_wali
    // First, let's see if it already exists to be safe
    $cols = $db->query("DESCRIBE catatan_wali")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('tahun_ajaran_id', $cols)) {
        $db->exec("ALTER TABLE catatan_wali ADD COLUMN tahun_ajaran_id INT UNSIGNED NOT NULL AFTER semester");
        
        // Populate existing records with currently active year
        $stmtActive = $db->query("SELECT id FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
        $activeYear = $stmtActive->fetch();
        if ($activeYear) {
            $db->exec("UPDATE catatan_wali SET tahun_ajaran_id = " . $activeYear['id']);
        }
        
        // Update Unique Key
        $db->exec("ALTER TABLE catatan_wali DROP INDEX uk_catatan");
        $db->exec("ALTER TABLE catatan_wali ADD UNIQUE KEY uk_catatan (siswa_id, tahun_ajaran_id, semester)");
        
        // Add Foreign Key
        $db->exec("ALTER TABLE catatan_wali ADD FOREIGN KEY (tahun_ajaran_id) REFERENCES tahun_ajaran(id) ON DELETE CASCADE");
    }

    echo "Migrating ekskul_nilai to use tahun_ajaran_id...\n";
    // 2. Change ekskul_nilai to use tahun_ajaran_id instead of string
    $cols = $db->query("DESCRIBE ekskul_nilai")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('tahun_ajaran_id', $cols)) {
        $db->exec("ALTER TABLE ekskul_nilai ADD COLUMN tahun_ajaran_id INT UNSIGNED AFTER semester");
        
        // Try to map existing string names to IDs
        $years = $db->query("SELECT id, nama FROM tahun_ajaran")->fetchAll();
        foreach ($years as $y) {
            $stmt = $db->prepare("UPDATE ekskul_nilai SET tahun_ajaran_id = ? WHERE tahun_ajaran = ?");
            $stmt->execute([$y['id'], $y['nama']]);
        }
        
        // Set remaining to active year if any
        $stmtActive = $db->query("SELECT id FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
        $activeYear = $stmtActive->fetch();
        if ($activeYear) {
            $db->exec("UPDATE ekskul_nilai SET tahun_ajaran_id = " . $activeYear['id'] . " WHERE tahun_ajaran_id IS NULL");
        }
        
        $db->exec("ALTER TABLE ekskul_nilai MODIFY COLUMN tahun_ajaran_id INT UNSIGNED NOT NULL");
        
        // Update Unique Key
        $db->exec("ALTER TABLE ekskul_nilai DROP INDEX uk_ekskul_nilai");
        $db->exec("ALTER TABLE ekskul_nilai ADD UNIQUE KEY uk_ekskul_nilai (ekskul_id, siswa_id, semester, tahun_ajaran_id)");
        
        // Add Foreign Key
        $db->exec("ALTER TABLE ekskul_nilai ADD FOREIGN KEY (tahun_ajaran_id) REFERENCES tahun_ajaran(id) ON DELETE CASCADE");
        
        // Remove old column
        $db->exec("ALTER TABLE ekskul_nilai DROP COLUMN tahun_ajaran");
    }

    $db->commit();
    echo "Migration successful!\n";
} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo "Migration failed: " . $e->getMessage() . "\n";
}
