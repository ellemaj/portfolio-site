<?php

namespace Tests\Unit\Models;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_default_role_is_user(): void
    {
        $user = new User();
        $this->assertEquals('user', $user->role);
    }

    public function test_deleted_at_is_null_by_default(): void
    {
        $user = new User();
        $this->assertNull($user->deleted_at);
    }

    public function test_can_set_name(): void
    {
        $user = new User();
        $user->firstName = 'Elmar';
        $user->lastName = 'van Loenhout';

        $this->assertEquals('Elmar', $user->firstName);
        $this->assertEquals('van Loenhout', $user->lastName);
    }

    public function test_can_set_email(): void
    {
        $user = new User();
        $user->email = 'elmar@example.com';
        $this->assertEquals('elmar@example.com', $user->email);
    }

    public function test_role_can_be_set_to_admin(): void
    {
        $user = new User();
        $user->role = 'admin';
        $this->assertEquals('admin', $user->role);
    }
}
