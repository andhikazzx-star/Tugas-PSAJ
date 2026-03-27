<?php
require_once __DIR__ . '/../bootstrap/app.php';
$db = getDB();
try {
    $check = $db->query("SHOW COLUMNS FROM nilai LIKE 'sakit'")->fetch();
    if ($check) echo "COLUMN_SAKIT_EXISTS\n";
    else echo "COLUMN_SAKIT_MISSING\n";
} catch (Exception $e) { echo "ERROR: " . $e->getMessage() . "\n"; }
