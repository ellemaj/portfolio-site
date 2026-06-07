<?php
require __DIR__ . '/vendor/autoload.php';

// Load .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
    }
}

$appDb  = $_ENV['APP_DB'] ?? 'database.sqlite';
$isMySQL = !str_ends_with($appDb, '.sqlite');

if ($isMySQL) {
    $host = $_ENV['DB_HOST'] ?? 'db';
    $name = $_ENV['DB_NAME'] ?? 'maestro';
    $user = $_ENV['DB_USER'] ?? 'root';
    $pass = $_ENV['DB_PASS'] ?? '';
    $dsn  = "mysql:host={$host};dbname={$name};charset=utf8mb4";
    $db   = new PDO($dsn, $user, $pass);
    $dir  = __DIR__ . '/database/mysql/';
    echo "Migrating MySQL ({$name} @ {$host})\n";
} else {
    $db  = new PDO("sqlite:{$appDb}");
    $dir = __DIR__ . '/database/';
    echo "Migrating SQLite ({$appDb})\n";
}

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$files = array_filter(scandir($dir), fn($f) => str_ends_with($f, '.sql'));
sort($files);

foreach ($files as $file) {
    echo "  → {$file} ... ";
    $sql = file_get_contents($dir . $file);
    $db->exec($sql);
    echo "done\n";
}

echo "\nMigration complete.\n";
