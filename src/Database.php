<?php

namespace Framework;

use PDO;
use PDOStatement;

class Database
{
    private PDO $connection;

    public function __construct(string $path = '')
    {
        if (str_ends_with($path, '.sqlite')) {
            $dsn = "sqlite:{$path}";
            $this->connection = new PDO($dsn);
        } else {
            $host = $_ENV['DB_HOST'] ?? 'db';
            $name = $_ENV['DB_NAME'] ?? 'maestro';
            $user = $_ENV['DB_USER'] ?? 'root';
            $pass = $_ENV['DB_PASS'] ?? '';
            $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";
            $this->connection = new PDO($dsn, $user, $pass);
        }
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    }

    public function query(string $query): PDOStatement|false
    {
        return $this->connection->query($query);
    }

    public function run(string $sql, array|null $params = null): PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function prepare(string $sql): PDOStatement
    {
        return $this->connection->prepare($sql);
    }

    public function exec(string $sql): false|int
    {
        return $this->connection->exec($sql);
    }

    public function getLastID(string|null $field = null): int
    {
        return (int)$this->connection->lastInsertId($field);
    }

    public function migrate(string $migrationsDirectory): void
    {
        $files = scandir($migrationsDirectory);
        if ($files === false) {
            die('Could not read database migration files');
        }
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            echo "Migrating: " . $file . "\n";
            if ($contents = file_get_contents($migrationsDirectory . $file)) {
                $this->connection->exec($contents);
            }
        }
    }
}