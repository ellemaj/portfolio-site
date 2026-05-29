<?php

namespace App\Repositories;

use App\Models\Course;

interface CourseRepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id): ?Course;
    public function updateGrade(int $id, ?float $grade): bool;
}