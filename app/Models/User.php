<?php

namespace App\Models;

class User
{
    public int $id;

    public string $firstName;

    public string $lastName;

    public string $email;

    public string $password;

    public string $role;

    public int $created_at;

    public int $last_login;

    public int | null $deleted_at;
}