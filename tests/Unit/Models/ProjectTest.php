<?php

namespace Tests\Unit\Models;

use App\Models\Project;
use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    public function test_url_is_null_by_default(): void
    {
        $project = new Project();
        $this->assertNull($project->url);
    }

    public function test_can_set_name_and_description(): void
    {
        $project = new Project();
        $project->name = 'ITDP Portfolio';
        $project->description = 'Mijn portfolio website.';

        $this->assertEquals('ITDP Portfolio', $project->name);
        $this->assertEquals('Mijn portfolio website.', $project->description);
    }

    public function test_can_set_url(): void
    {
        $project = new Project();
        $project->url = 'https://example.com';

        $this->assertEquals('https://example.com', $project->url);
    }

    public function test_can_set_sort_order(): void
    {
        $project = new Project();
        $project->sort_order = 3;

        $this->assertEquals(3, $project->sort_order);
    }

    public function test_can_set_created_at(): void
    {
        $project = new Project();
        $now = time();
        $project->created_at = $now;

        $this->assertEquals($now, $project->created_at);
    }
}
