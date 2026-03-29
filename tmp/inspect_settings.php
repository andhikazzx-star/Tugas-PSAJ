<?php
require_once __DIR__ . '/../bootstrap/app.php';
$db = getDB();
$sql = $db->query("SHOW CREATE TABLE settings")->fetch(PDO::FETCH_NUM)[1];
echo $sql . ";\n";
echo "--- DATA ---\n";
$rows = $db->query("SELECT * FROM settings")->fetchAll();
foreach ($rows as $row) {
    echo "INSERT INTO settings (`key`, `value`) VALUES ('{$row['key']}', '{$row['value']}');\n";
}
