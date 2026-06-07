<?php

namespace App\Repositories;

use App\Models\User;
use Framework\Database;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private Database $db)
    {
    }

    public function findByEmail(string $email): ?User
    {
        $data = $this->db->run(
            "SELECT * FROM users WHERE email = :email",
            ["email" => $email]
        )->fetch();

        if (!$data instanceof \stdClass) {
            return null;
        }

        return $this->mapToUser($data);
    }

    public function findById(int $id): ?User
    {
        $data = $this->db->run(
            "SELECT * FROM users WHERE id = :id",
            ["id" => $id]
        )->fetch();

        if (!$data instanceof \stdClass) {
            return null;
        }

        return $this->mapToUser($data);
    }

    public function create(User $user): ?User
    {
        $this->db->run("
            INSERT INTO users (firstName, lastName, email, password, role, created_at, deleted_at)
            VALUES (:firstName, :lastName, :email, :password, :role, :created_at, :deleted_at)
        ", [
            "firstName"  => $user->firstName,
            "lastName"   => $user->lastName,
            "email"      => $user->email,
            "password"   => $user->password,
            "role"       => $user->role,
            "created_at" => $user->created_at,
            "deleted_at" => $user->deleted_at
        ]);

        $user->id = $this->db->getLastID();
        return $user;
    }

    private function mapToUser(\stdClass $data): User
    {
        $user = new User();
        $user->id         = (int) $data->id;
        $user->firstName  = $data->firstName;
        $user->lastName   = $data->lastName;
        $user->email      = $data->email;
        $user->password   = $data->password;
        $user->role       = $data->role;
        $user->created_at = (int) ($data->created_at ?? 0);
        $user->deleted_at = isset($data->deleted_at) ? (int) $data->deleted_at : null;
        return $user;
    }
}
