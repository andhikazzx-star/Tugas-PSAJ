<?php
require_once __DIR__ . '/../bootstrap/app.php';
$db = getDB();
echo "Checking table structure...\n";
$tables = ['nilai', 'mapel', 'catatan_wali', 'absensi_mapel'];
foreach ($tables as $t) {
    echo "\nTable: $t\n";
    try {
        $cols = $db->query("DESC $t")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $c) {
            echo " - {$c['Field']} ({$c['Type']})\n";
        }
    } catch (Exception $e) {
        echo " ❌ Table not found or error: " . $e->getMessage() . "\n";
    }
}
