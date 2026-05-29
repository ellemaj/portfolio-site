<?php

namespace App\Models;

class Profile
{
    public int $id;

    public string $intro;
    public string $bio;

    public ?string $birthdate = null;
    public ?string $education = null;
    public ?string $experience = null;

    public string $skills;
    public string $traits;

    public ?string $github = null;
    public ?string $linkedin = null;
    public ?string $spotify = null;
    public ?string $discord = null;

    public ?string $image = null;
}