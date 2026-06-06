<?php

namespace App\Repositories;

use App\Models\Post;

interface PostRepositoryInterface
{
    /** @return mixed[] */
    public function findAllPublished(): array;

    /** @return mixed[] */
    public function findAll(): array;

    public function findById(int $id): ?Post;

    public function findBySlug(string $slug): ?Post;

    public function create(Post $post): ?Post;

    public function update(int $id, Post $post): ?Post;

    public function delete(int $id): bool;

    public function undoDelete(int $id): bool;
}
