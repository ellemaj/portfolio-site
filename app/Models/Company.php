<?php

namespace App\Models;

class Company
{
    public int $id;

    public string $name;
    public string $description;

    public string $service1_title;
    public string $service1_description;

    public string $service2_title;
    public string $service2_description;

    public string $service3_title;
    public string $service3_description;

    public ?string $logo = null;
    public ?string $photo1 = null;
    public ?string $photo2 = null;
}
