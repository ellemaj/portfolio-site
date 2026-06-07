<?php

namespace App\Repositories;

use App\Models\Project;

interface ProjectRepositoryInterface
{
    /** @return Project[] */
    public function findAll(): array;

    public function findById(int $id): ?Project;

    public function create(Project $project): ?Project;

    public function update(int $id, Project $project): ?Project;

    public function delete(int $id): bool;
}
