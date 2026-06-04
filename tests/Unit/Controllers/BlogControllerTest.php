<?php

namespace Tests\Unit\Controllers;

use App\Controllers\BlogController;
use App\Models\Post;
use App\Repositories\PostRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use PHPUnit\Framework\TestCase;

class BlogControllerTest extends TestCase
{
    private ResponseFactory $responseFactory;
    private PostRepositoryInterface $postRepository;
    private BlogController $controller;

    protected function setUp(): void
    {
        $this->responseFactory = $this->createMock(ResponseFactory::class);
        $this->postRepository = $this->createMock(PostRepositoryInterface::class);
        $this->controller = new BlogController($this->responseFactory, $this->postRepository);
    }

    private function makeRequest(array $query = [], array $post = []): Request
    {
        return new Request('GET', '/', $query, $post);
    }

    public function test_index_returns_response(): void
    {
        $this->postRepository->method('findAllPublished')->willReturn([]);
        $this->responseFactory->method('view')->willReturn(new Response('', 200));

        $response = $this->controller->index($this->makeRequest());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->responseCode);
    }

    public function test_show_returns_not_found_when_no_slug(): void
    {
        $this->responseFactory->method('notFound')->willReturn(new Response('', 404));

        $response = $this->controller->show($this->makeRequest());

        $this->assertEquals(404, $response->responseCode);
    }

    public function test_show_returns_view_when_slug_given(): void
    {
        $this->responseFactory->method('view')->willReturn(new Response('', 200));

        $response = $this->controller->show($this->makeRequest(['slug' => 'mijn-post']));

        $this->assertEquals(200, $response->responseCode);
    }

    public function test_manage_returns_response(): void
    {
        $this->postRepository->method('findAll')->willReturn([]);
        $this->responseFactory->method('view')->willReturn(new Response('', 200));

        $response = $this->controller->manage($this->makeRequest());

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_show_create_returns_response(): void
    {
        $this->responseFactory->method('view')->willReturn(new Response('', 200));

        $response = $this->controller->showCreate($this->makeRequest());

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_show_edit_returns_not_found_when_post_missing(): void
    {
        $this->postRepository->method('findById')->willReturn(null);
        $this->responseFactory->method('notFound')->willReturn(new Response('', 404));

        $response = $this->controller->showEdit($this->makeRequest(['id' => '999']));

        $this->assertEquals(404, $response->responseCode);
    }

    public function test_show_edit_returns_view_when_post_found(): void
    {
        $post = new Post();
        $post->id = 1;
        $post->title = 'Test';
        $post->slug = 'test';
        $post->preview = 'preview';
        $post->content = 'content';
        $post->status = 'published';
        $post->publication_date = time();
        $post->created_at = time();

        $this->postRepository->method('findById')->willReturn($post);
        $this->responseFactory->method('view')->willReturn(new Response('', 200));

        $response = $this->controller->showEdit($this->makeRequest(['id' => '1']));

        $this->assertEquals(200, $response->responseCode);
    }
}
