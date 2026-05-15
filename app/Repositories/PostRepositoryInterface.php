<?php

namespace App\Repositories;

use App\Models\Post;

interface PostRepositoryInterface
{
    public function findAllPublished(): array;

    public function findBySlug(string $slug): ?Post;

    public function create(string $title, string $slug, string $preview, string $content, string $status): void;
}