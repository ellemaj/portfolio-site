<?php

namespace Tests\Unit\Models;

use App\Models\Post;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    public function test_deleted_at_is_null_by_default(): void
    {
        $post = new Post();
        $this->assertNull($post->deleted_at);
    }

    public function test_can_set_title_and_slug(): void
    {
        $post = new Post();
        $post->title = 'Mijn eerste post';
        $post->slug = 'mijn-eerste-post';

        $this->assertEquals('Mijn eerste post', $post->title);
        $this->assertEquals('mijn-eerste-post', $post->slug);
    }

    public function test_status_can_be_published(): void
    {
        $post = new Post();
        $post->status = 'published';
        $this->assertEquals('published', $post->status);
    }

    public function test_status_can_be_draft(): void
    {
        $post = new Post();
        $post->status = 'draft';
        $this->assertEquals('draft', $post->status);
    }

    public function test_can_set_content_and_preview(): void
    {
        $post = new Post();
        $post->content = 'Dit is de inhoud.';
        $post->preview = 'Korte preview.';

        $this->assertEquals('Dit is de inhoud.', $post->content);
        $this->assertEquals('Korte preview.', $post->preview);
    }
}
