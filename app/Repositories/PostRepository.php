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

    public function create(Post $post): ?Post
    {
        $this->db->run("
        INSERT INTO posts
        (title, slug, preview, content, status, publication_date, created_at, deleted_at)
        VALUES
        (:title, :slug, :preview, :content, :status, :publication_date, :created_at, :deleted_at)
        ", [
            "title" => $post->title,
            "slug" => $post->slug,
            "preview" => $post->preview,
            "content" => $post->content,
            "status" => $post->status,
            "publication_date" => $post->publication_date,
            "created_at" => $post->created_at,
            "deleted_at" => $post->deleted_at
        ]);

        $post->id = $this->db->getLastId();
        return $post;
    }

    public function update(int $id, Post $post): ?Post
    {
        $this->db->run("
        UPDATE posts SET
            title = :title,
            slug = :slug,
            preview = :preview,
            content = :content,
            status = :status,
            publication_date = :publication_date
        WHERE id = :id
        ", [
            "id" => $id,
            "title" => $post->title,
            "slug" => $post->slug,
            "preview" => $post->preview,
            "content" => $post->content,
            "status" => $post->status,
            "publication_date" => $post->publication_date,
        ]);

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $this->db->run("
        UPDATE posts SET
            deleted_at = :deleted_at
        WHERE id = :id
    ", [
            "id" => $id,
            "deleted_at" => time() + 120
        ]);

        return $this->findById($id)->deleted_at;
    }

    public function undoDelete(int $id): bool
    {
        $this->db->run("
        UPDATE posts SET
            deleted_at = :deleted_at
        WHERE id = :id
    ", [
            "id" => $id,
            "deleted_at" => null
        ]);

        return !$this->findById($id)->deleted_at;
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