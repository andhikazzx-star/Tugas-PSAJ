<?php
require_once __DIR__ . '/../bootstrap/app.php';
$db = getDB();
$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $table) {
    echo "TABLE: $table\n";
    $cols = $db->query("DESCRIBE $table")->fetchAll();
    foreach ($cols as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }``
}
