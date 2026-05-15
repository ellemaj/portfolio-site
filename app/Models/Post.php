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
    public string $created_at;
}