<?php

namespace App\Repositories;

use App\Models\Course;
use Framework\Database;

class CourseRepository implements CourseRepositoryInterface
{
    public function __construct(private Database $db)
    {
    }

    /** @return Course[] */
    public function findAll(): array
    {
        /** @var \stdClass[] $rows */
        $rows = $this->db->run("SELECT * FROM courses ORDER BY id ASC")->fetchAll() ?: [];
        return array_map([$this, 'mapToCourse'], $rows);
    }

    public function findById(int $id): ?Course
    {
        $data = $this->db->run(
            "SELECT * FROM courses WHERE id = :id",
            ["id" => $id]
        )->fetch();

        if (!$data instanceof \stdClass) {
            return null;
        }

        return $this->mapToCourse($data);
    }

    public function updateGrade(int $id, ?float $grade): bool
    {
        $this->db->run(
            "UPDATE courses SET grade = :grade WHERE id = :id",
            ["grade" => $grade, "id" => $id]
        );

        return true;
    }

    private function mapToCourse(\stdClass $data): Course
    {
        $course = new Course();
        $course->id        = (int) $data->id;
        $course->blok      = $data->blok;
        $course->name      = $data->name;
        $course->ec        = (float) $data->ec;
        $course->exam_type = $data->exam_type;
        $course->grade     = isset($data->grade) ? (float) $data->grade : null;
        $course->created_at = (int) ($data->created_at ?? 0);
        return $course;
    }
}
