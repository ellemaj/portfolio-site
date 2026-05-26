<?php

namespace App\Repositories;

use Framework\Database;
use App\Models\Post;

class PostRepository implements PostRepositoryInterface
{
    public function __construct(private Database $db) {}

    public function findAllPublished(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE status = 'published'");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM posts ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?Post
    {
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) return null;

        return $this->mapToPost($data);
    }

    public function findBySlug(string $slug): ?Post
    {
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE slug = ?");
        $stmt->execute([$slug]);
        $data = $stmt->fetch();

        if (!$data) return null;

        return $this->mapToPost($data);
    }

    public function create(string $title, string $slug, string $preview, string $content, string $status): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO posts (title, slug, preview, content, status) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $slug, $preview, $content, $status]);
    }

    public function update(int $id, string $title, string $slug, string $preview, string $content, string $status): void
    {
        $stmt = $this->db->prepare("
            UPDATE posts SET title = ?, slug = ?, preview = ?, content = ?, status = ?
            WHERE id = ?
        ");
        $stmt->execute([$title, $slug, $preview, $content, $status, $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$id]);
    }

    private function mapToPost(object $data): Post
    {
        $post = new Post();
        $post->id = $data->id;
        $post->title = $data->title;
        $post->slug = $data->slug;
        $post->preview = $data->preview;
        $post->content = $data->content;
        $post->status = $data->status;
        return $post;
    }
}