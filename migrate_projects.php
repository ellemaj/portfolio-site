<?php
$db = new PDO('sqlite:database.sqlite');
$db->exec("DROP TABLE IF EXISTS projects");
$sql = file_get_contents(__DIR__ . '/database/5_projects.sql');
$db->exec($sql);
echo "Projects migrated.\n";
foreach ($db->query('SELECT id, name FROM projects')->fetchAll(PDO::FETCH_OBJ) as $r) {
    echo $r->id . ' | ' . $r->name . "\n";
}
