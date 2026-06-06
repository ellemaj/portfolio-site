<?php

namespace App\Models;

class Course
{
    public int $id;
    public string $blok;
    public string $name;
    public float $ec;
    public string $exam_type;
    public float|null $grade = null;
    public int $created_at;
}
