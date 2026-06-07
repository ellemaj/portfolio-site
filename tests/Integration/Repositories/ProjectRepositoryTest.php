<?php

namespace Tests\Integration\Repositories;

use App\Models\Project;
use App\Repositories\ProjectRepository;
use Framework\Database;
use PHPUnit\Framework\TestCase;

class ProjectRepositoryTest extends TestCase
{
    private Database $db;
    private ProjectRepository $repo;
    private string $dbPath;

    protected function setUp(): void
    {
        $this->dbPath = sys_get_temp_dir() . '/test_' . uniqid() . '.sqlite';
        $this->db = new Database($this->dbPath);

        $this->db->exec("
            CREATE TABLE projects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                description TEXT NOT NULL,
                url TEXT,
                sort_order INTEGER DEFAULT 0,
                created_at INTEGER DEFAULT (strftime('%s', 'now'))
            )
        ");

        $this->repo = new ProjectRepository($this->db);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->dbPath)) {
            unlink($this->dbPath);
        }
    }

    private function createTestProject(string $name = 'Test Project', int $sortOrder = 0): Project
    {
        $project = new Project();
        $project->name = $name;
        $project->description = 'Een testbeschrijving.';
        $project->url = 'https://example.com';
        $project->sort_order = $sortOrder;
        $project->created_at = time();

        return $this->repo->create($project);
    }

    public function test_create_returns_project_with_id(): void
    {
        $project = $this->createTestProject();

        $this->assertNotNull($project->id);
        $this->assertGreaterThan(0, $project->id);
    }

    public function test_create_saves_correct_data(): void
    {
        $project = $this->createTestProject('Mijn Project');

        $this->assertEquals('Mijn Project', $project->name);
        $this->assertEquals('Een testbeschrijving.', $project->description);
        $this->assertEquals('https://example.com', $project->url);
    }

    public function test_find_by_id_returns_project(): void
    {
        $created = $this->createTestProject();

        $found = $this->repo->findById($created->id);

        $this->assertNotNull($found);
        $this->assertEquals($created->id, $found->id);
    }

    public function test_find_by_id_returns_null_when_not_found(): void
    {
        $result = $this->repo->findById(9999);

        $this->assertNull($result);
    }

    public function test_find_all_returns_all_projects(): void
    {
        $this->createTestProject('Project A');
        $this->createTestProject('Project B');

        $results = $this->repo->findAll();

        $this->assertCount(2, $results);
    }

    public function test_find_all_returns_empty_array_when_no_projects(): void
    {
        $results = $this->repo->findAll();

        $this->assertIsArray($results);
        $this->assertCount(0, $results);
    }

    public function test_find_all_orders_by_sort_order(): void
    {
        $this->createTestProject('Project C', 3);
        $this->createTestProject('Project A', 1);
        $this->createTestProject('Project B', 2);

        $results = $this->repo->findAll();

        $this->assertEquals('Project A', $results[0]->name);
        $this->assertEquals('Project B', $results[1]->name);
        $this->assertEquals('Project C', $results[2]->name);
    }

    public function test_update_changes_name(): void
    {
        $project = $this->createTestProject();
        $project->name = 'Bijgewerkte Naam';
        $project->description = 'Nieuwe beschrijving.';

        $updated = $this->repo->update($project->id, $project);

        $this->assertEquals('Bijgewerkte Naam', $updated->name);
    }

    public function test_delete_removes_project(): void
    {
        $project = $this->createTestProject();

        $result = $this->repo->delete($project->id);

        $this->assertTrue($result);
        $this->assertNull($this->repo->findById($project->id));
    }
}
