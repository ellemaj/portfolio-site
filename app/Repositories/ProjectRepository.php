<?php

namespace App\Repositories;

use App\Models\Project;
use Framework\Database;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function __construct(private Database $db)
    {
    }

    /** @return Project[] */
    public function findAll(): array
    {
        /** @var \stdClass[] $rows */
        $rows = $this->db->run(
            "SELECT * FROM projects ORDER BY sort_order ASC, id ASC"
        )->fetchAll() ?: [];
        return array_map([$this, 'mapToProject'], $rows);
    }

    public function findById(int $id): ?Project
    {
        $data = $this->db->run(
            "SELECT * FROM projects WHERE id = :id",
            ["id" => $id]
        )->fetch();

        if (!$data instanceof \stdClass) {
            return null;
        }

        return $this->mapToProject($data);
    }

    public function create(Project $project): ?Project
    {
        $this->db->run(
            "INSERT INTO projects (name, description, url, sort_order, created_at)
            VALUES (:name, :description, :url, :sort_order, :created_at)",
            [
                'name'        => $project->name,
                'description' => $project->description,
                'url'         => $project->url,
                'sort_order'  => $project->sort_order,
                'created_at'  => $project->created_at,
            ]
        );

        $project->id = $this->db->getLastID();
        return $project;
    }

    public function update(int $id, Project $project): ?Project
    {
        $this->db->run(
            "UPDATE projects SET
                name        = :name,
                description = :description,
                url         = :url,
                sort_order  = :sort_order
            WHERE id = :id",
            [
                'id'          => $id,
                'name'        => $project->name,
                'description' => $project->description,
                'url'         => $project->url,
                'sort_order'  => $project->sort_order,
            ]
        );

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $this->db->run("DELETE FROM projects WHERE id = :id", ["id" => $id]);
        return $this->findById($id) === null;
    }

    private function mapToProject(\stdClass $data): Project
    {
        $project = new Project();
        $project->id          = (int) $data->id;
        $project->name        = $data->name;
        $project->description = $data->description;
        $project->url         = $data->url ?? null;
        $project->sort_order  = (int) ($data->sort_order ?? 0);
        $project->created_at  = (int) ($data->created_at ?? 0);
        return $project;
    }
}
