<?php

namespace App\Repositories;

use App\Models\User;
use Framework\Database;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private Database $db) {}

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $data = $stmt->fetch();

        if (!$data) return null;

        $user = new User();
        $user->id = $data->id;
        $user->username = $data->username;
        $user->name = $data->name;
        $user->email = $data->email;
        $user->password = $data->password;
        $user->role = $data->role;

        return $user;
    }

    public function create(string $username, string $name, string $email, string $password): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (username, name, email, password)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$username, $name, $email, $password]);
    }
}