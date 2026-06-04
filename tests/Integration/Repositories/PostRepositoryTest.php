<?php

namespace Tests\Integration\Repositories;

use App\Models\Post;
use App\Repositories\PostRepository;
use Framework\Database;
use PHPUnit\Framework\TestCase;

class PostRepositoryTest extends TestCase
{
    private Database $db;
    private PostRepository $repo;
    private string $dbPath;

    protected function setUp(): void
    {
        $this->dbPath = sys_get_temp_dir() . '/test_' . uniqid() . '.sqlite';
        $this->db = new Database($this->dbPath);

        $this->db->exec("
            CREATE TABLE posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                preview TEXT NOT NULL,
                content TEXT NOT NULL,
                status TEXT DEFAULT 'draft',
                publication_date INTEGER NULL,
                created_at INTEGER,
                deleted_at INTEGER NULL
            )
        ");

        $this->repo = new PostRepository($this->db);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->dbPath)) {
            unlink($this->dbPath);
        }
    }

    private function createTestPost(string $slug = 'test-post', string $status = 'published'): Post
    {
        $post = new Post();
        $post->title = 'Test Post';
        $post->slug = $slug;
        $post->preview = 'Dit is een korte samenvatting.';
        $post->content = 'Dit is de volledige inhoud van de post.';
        $post->status = $status;
        $post->publication_date = time();
        $post->created_at = time();
        $post->deleted_at = null;

        return $this->repo->create($post);
    }

    public function test_create_returns_post_with_id(): void
    {
        $post = $this->createTestPost();

        $this->assertNotNull($post->id);
        $this->assertGreaterThan(0, $post->id);
    }

    public function test_create_saves_correct_data(): void
    {
        $post = $this->createTestPost('mijn-post');

        $this->assertEquals('Test Post', $post->title);
        $this->assertEquals('mijn-post', $post->slug);
    }

    public function test_find_by_id_returns_post(): void
    {
        $created = $this->createTestPost();

        $found = $this->repo->findById($created->id);

        $this->assertNotNull($found);
        $this->assertEquals($created->id, $found->id);
    }

    public function test_find_by_id_returns_null_when_not_found(): void
    {
        $result = $this->repo->findById(9999);

        $this->assertNull($result);
    }

    public function test_find_by_slug_returns_post(): void
    {
        $this->createTestPost('mijn-slug');

        $found = $this->repo->findBySlug('mijn-slug');

        $this->assertNotNull($found);
        $this->assertEquals('mijn-slug', $found->slug);
    }

    public function test_find_by_slug_returns_null_when_not_found(): void
    {
        $result = $this->repo->findBySlug('bestaat-niet');

        $this->assertNull($result);
    }

    public function test_find_all_published_only_returns_published_posts(): void
    {
        $this->createTestPost('gepubliceerd', 'published');
        $this->createTestPost('concept', 'draft');

        $results = $this->repo->findAllPublished();

        $this->assertCount(1, $results);
        $this->assertEquals('published', $results[0]->status);
    }

    public function test_find_all_returns_all_posts(): void
    {
        $this->createTestPost('post-1', 'published');
        $this->createTestPost('post-2', 'draft');

        $results = $this->repo->findAll();

        $this->assertCount(2, $results);
    }

    public function test_update_changes_title(): void
    {
        $post = $this->createTestPost();
        $post->title = 'Bijgewerkte Titel';

        $updated = $this->repo->update($post->id, $post);

        $this->assertEquals('Bijgewerkte Titel', $updated->title);
    }

    public function test_delete_sets_deleted_at(): void
    {
        $post = $this->createTestPost();

        $this->repo->delete($post->id);

        $deleted = $this->repo->findById($post->id);
        $this->assertNotNull($deleted->deleted_at);
    }

    public function test_undo_delete_clears_deleted_at(): void
    {
        $post = $this->createTestPost();
        $this->repo->delete($post->id);

        $this->repo->undoDelete($post->id);

        $restored = $this->repo->findById($post->id);
        $this->assertNull($restored->deleted_at);
    }
}
