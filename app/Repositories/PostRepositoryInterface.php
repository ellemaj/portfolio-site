<?php

namespace App\Repositories;

use App\Models\Post;

interface PostRepositoryInterface
{
    public function findAllPublished(): array;

    public function findAll(): array;

    public function findById(int $id): ?Post;

    public function findBySlug(string $slug): ?Post;

    public function create(Post $post): ?Post;

    public function update(int $id, Post $post): ?Post;
    
    public function delete(int $id): bool;
}