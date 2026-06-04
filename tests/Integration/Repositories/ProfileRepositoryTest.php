<?php

namespace Tests\Integration\Repositories;

use App\Models\Profile;
use App\Repositories\ProfileRepository;
use Framework\Database;
use PHPUnit\Framework\TestCase;

class ProfileRepositoryTest extends TestCase
{
    private Database $db;
    private ProfileRepository $repo;
    private string $dbPath;

    protected function setUp(): void
    {
        $this->dbPath = sys_get_temp_dir() . '/test_' . uniqid() . '.sqlite';
        $this->db = new Database($this->dbPath);

        $this->db->exec("
            CREATE TABLE profile (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                intro TEXT NOT NULL,
                bio TEXT NOT NULL,
                birthdate TEXT NULL,
                education TEXT NULL,
                experience TEXT NULL,
                skills TEXT NOT NULL,
                traits TEXT NOT NULL,
                github TEXT NULL,
                linkedin TEXT NULL,
                spotify TEXT NULL,
                discord TEXT NULL,
                image TEXT NULL
            )
        ");

        $this->repo = new ProfileRepository($this->db);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->dbPath)) {
            unlink($this->dbPath);
        }
    }

    private function insertProfile(): void
    {
        $this->db->run(
            "INSERT INTO profile (intro, bio, skills, traits)
             VALUES (:intro, :bio, :skills, :traits)",
            [
                'intro'  => 'Ik ben een student.',
                'bio'    => 'Ik studeer HBO-ICT.',
                'skills' => 'PHP|Docker|Git',
                'traits' => 'Behulpzaam|Leergierig',
            ]
        );
    }

    public function test_get_returns_null_when_no_profile(): void
    {
        $result = $this->repo->get();

        $this->assertNull($result);
    }

    public function test_get_returns_profile(): void
    {
        $this->insertProfile();

        $profile = $this->repo->get();

        $this->assertNotNull($profile);
        $this->assertEquals('Ik ben een student.', $profile->intro);
        $this->assertEquals('PHP|Docker|Git', $profile->skills);
    }

    public function test_get_returns_null_for_optional_fields_when_empty(): void
    {
        $this->insertProfile();

        $profile = $this->repo->get();

        $this->assertNull($profile->github);
        $this->assertNull($profile->linkedin);
        $this->assertNull($profile->image);
    }

    public function test_update_saves_changes(): void
    {
        $this->insertProfile();

        $profile = $this->repo->get();
        $profile->intro = 'Bijgewerkte intro.';
        $profile->bio = 'Bijgewerkte bio.';

        $this->repo->update($profile);

        $updated = $this->repo->get();
        $this->assertEquals('Bijgewerkte intro.', $updated->intro);
        $this->assertEquals('Bijgewerkte bio.', $updated->bio);
    }

    public function test_update_returns_true(): void
    {
        $this->insertProfile();

        $profile = $this->repo->get();
        $result = $this->repo->update($profile);

        $this->assertTrue($result);
    }
}
