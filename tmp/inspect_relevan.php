<?php
require_once __DIR__ . '/../bootstrap/app.php';
$db = getDB();
$table = 'kelas';
echo "TABLE: $table\n";
$cols = $db->query("DESCRIBE $table")->fetchAll();
foreach ($cols as $col) {
    echo "  - {$col['Field']} ({$col['Type']})\n";
}
echo "\nTABLE: tahun_ajaran\n";
$cols = $db->query("DESCRIBE tahun_ajaran")->fetchAll();
foreach ($cols as $col) {
    echo "  - {$col['Field']} ({$col['Type']})\n";
}
echo "\nTABLE: ekskul_nilai\n";
$cols = $db->query("DESCRIBE ekskul_nilai")->fetchAll();
foreach ($cols as $col) {
    echo "  - {$col['Field']} ({$col['Type']})\n";
}
echo "\nTABLE: nilai\n";
$cols = $db->query("DESCRIBE nilai")->fetchAll();
foreach ($cols as $col) {
    echo "  - {$col['Field']} ({$col['Type']})\n";
}
