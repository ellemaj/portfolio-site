<?php

namespace App\Models;

class Project
{
    public int $id;
    public string $name;
    public string $description;
    public ?string $url = null;
    public int $sort_order;
    public int $created_at;
}
