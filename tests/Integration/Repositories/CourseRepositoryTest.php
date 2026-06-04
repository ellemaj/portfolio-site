<?php

namespace Tests\Integration\Repositories;

use App\Models\Course;
use App\Repositories\CourseRepository;
use Framework\Database;
use PHPUnit\Framework\TestCase;

class CourseRepositoryTest extends TestCase
{
    private Database $db;
    private CourseRepository $repo;
    private string $dbPath;

    protected function setUp(): void
    {
        $this->dbPath = sys_get_temp_dir() . '/test_' . uniqid() . '.sqlite';
        $this->db = new Database($this->dbPath);

        $this->db->exec("
            CREATE TABLE courses (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                blok TEXT NOT NULL,
                name TEXT NOT NULL,
                ec REAL NOT NULL,
                exam_type TEXT NOT NULL,
                grade REAL NULL,
                created_at INTEGER
            )
        ");

        $this->repo = new CourseRepository($this->db);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->dbPath)) {
            unlink($this->dbPath);
        }
    }

    private function createTestCourse(string $name = 'Programmeren', ?float $grade = null): Course
    {
        $this->db->run(
            "INSERT INTO courses (blok, name, ec, exam_type, grade, created_at)
             VALUES (:blok, :name, :ec, :exam_type, :grade, :created_at)",
            [
                'blok'      => '1',
                'name'      => $name,
                'ec'        => 5.0,
                'exam_type' => 'Schriftelijk',
                'grade'     => $grade,
                'created_at' => time(),
            ]
        );

        return $this->repo->findById($this->db->getLastID());
    }

    public function test_find_all_returns_all_courses(): void
    {
        $this->createTestCourse('Programmeren');
        $this->createTestCourse('Databases');

        $results = $this->repo->findAll();

        $this->assertCount(2, $results);
    }

    public function test_find_all_returns_empty_array_when_no_courses(): void
    {
        $results = $this->repo->findAll();

        $this->assertCount(0, $results);
    }

    public function test_find_by_id_returns_course(): void
    {
        $created = $this->createTestCourse('Netwerken');

        $found = $this->repo->findById($created->id);

        $this->assertNotNull($found);
        $this->assertEquals('Netwerken', $found->name);
    }

    public function test_find_by_id_returns_null_when_not_found(): void
    {
        $result = $this->repo->findById(9999);

        $this->assertNull($result);
    }

    public function test_grade_is_null_by_default(): void
    {
        $course = $this->createTestCourse('Security');

        $this->assertNull($course->grade);
    }

    public function test_update_grade_saves_new_grade(): void
    {
        $course = $this->createTestCourse('OOP');

        $this->repo->updateGrade($course->id, 8.5);

        $updated = $this->repo->findById($course->id);
        $this->assertEquals(8.5, $updated->grade);
    }

    public function test_update_grade_can_set_grade_to_null(): void
    {
        $course = $this->createTestCourse('Linux', 7.0);

        $this->repo->updateGrade($course->id, null);

        $updated = $this->repo->findById($course->id);
        $this->assertNull($updated->grade);
    }
}
