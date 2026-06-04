<?php

namespace Tests\Integration\Repositories;

use App\Models\User;
use App\Repositories\UserRepository;
use Framework\Database;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private Database $db;
    private UserRepository $repo;
    private string $dbPath;

    protected function setUp(): void
    {
        $this->dbPath = sys_get_temp_dir() . '/test_' . uniqid() . '.sqlite';
        $this->db = new Database($this->dbPath);

        $this->db->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                firstName TEXT NOT NULL,
                lastName TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                role TEXT DEFAULT 'user',
                created_at INTEGER,
                last_login INTEGER NULL,
                deleted_at INTEGER NULL
            )
        ");

        $this->repo = new UserRepository($this->db);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->dbPath)) {
            unlink($this->dbPath);
        }
    }

    private function createTestUser(string $email = 'test@example.com', string $role = 'user'): User
    {
        $user = new User();
        $user->firstName = 'Test';
        $user->lastName = 'Gebruiker';
        $user->email = $email;
        $user->password = password_hash('wachtwoord123', PASSWORD_DEFAULT);
        $user->role = $role;
        $user->created_at = time();
        $user->deleted_at = null;

        return $this->repo->create($user);
    }

    public function test_create_returns_user_with_id(): void
    {
        $user = $this->createTestUser();

        $this->assertNotNull($user->id);
        $this->assertGreaterThan(0, $user->id);
    }

    public function test_create_saves_correct_data(): void
    {
        $user = $this->createTestUser('elmar@example.com');

        $this->assertEquals('Test', $user->firstName);
        $this->assertEquals('elmar@example.com', $user->email);
    }

    public function test_find_by_email_returns_user(): void
    {
        $this->createTestUser('zoeken@example.com');

        $found = $this->repo->findByEmail('zoeken@example.com');

        $this->assertNotNull($found);
        $this->assertEquals('zoeken@example.com', $found->email);
    }

    public function test_find_by_email_returns_null_when_not_found(): void
    {
        $result = $this->repo->findByEmail('bestaaniet@example.com');

        $this->assertNull($result);
    }

    public function test_find_by_id_returns_user(): void
    {
        $created = $this->createTestUser();

        $found = $this->repo->findById($created->id);

        $this->assertNotNull($found);
        $this->assertEquals($created->id, $found->id);
        $this->assertEquals('Test', $found->firstName);
    }

    public function test_find_by_id_returns_null_when_not_found(): void
    {
        $result = $this->repo->findById(9999);

        $this->assertNull($result);
    }

    public function test_create_admin_user(): void
    {
        $user = $this->createTestUser('admin@example.com', 'admin');

        $this->assertEquals('admin', $user->role);
    }

    public function test_password_is_stored_hashed(): void
    {
        $user = $this->createTestUser();
        $found = $this->repo->findById($user->id);

        $this->assertTrue(password_verify('wachtwoord123', $found->password));
    }
}
