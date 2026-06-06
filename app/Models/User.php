<?php

namespace App\Models;

class User
{
    public int $id;

    public string $firstName;

    public string $lastName;

    public string $email;

    public string $password;

    public string $role = 'user';

    public int $created_at;

    public int|null $deleted_at = null;
}
