<?php

namespace App\Models;

class Post
{
    public int $id;
    public string $title;
    public string $slug;
    public string $preview;
    public string $content;
    public string $status;
    public int $publication_date;
    public int $created_at;
    public int | null $deleted_at = null;
}
