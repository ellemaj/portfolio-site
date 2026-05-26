<?php

namespace App\Controllers;

use App\Middleware\AdminMiddleware;
use App\Repositories\PostRepositoryInterface;
use Framework\Response;
use Framework\ResponseFactory;

class BlogController
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private PostRepositoryInterface $posts,
        private AdminMiddleware $adminMiddleware
    ) {}

    public function index(): Response
    {
        $posts = $this->posts->findAllPublished();

        return $this->responseFactory->view('blog/index.html.twig', [
            'posts' => $posts,
            'active' => 'blog'
        ]);
    }

    public function show(string $slug): Response
    {
        $post = $this->posts->findBySlug($slug);

        if (!$post) {
            return $this->responseFactory->view('404.html.twig');
        }

        return $this->responseFactory->view('blog/post.html.twig', [
            'post' => $post,
            'active' => 'blog'
        ]);
    }

    public function manage(): Response
    {
        if ($response = $this->adminMiddleware->handle()) return $response;

        $posts = $this->posts->findAll();

        return $this->responseFactory->view('blog/manage.html.twig', [
            'posts' => $posts,
            'active' => 'blog'
        ]);
    }

    public function showCreate(): Response
    {
        if ($response = $this->adminMiddleware->handle()) return $response;

        return $this->responseFactory->view('blog/create.html.twig', [
            'active' => 'blog'
        ]);
    }

    public function create(): Response
    {
        if ($response = $this->adminMiddleware->handle()) return $response;

        $title   = $_POST['title'] ?? '';
        $slug    = $_POST['slug'] ?? '';
        $preview = $_POST['preview'] ?? '';
        $content = $_POST['content'] ?? '';
        $status  = $_POST['status'] ?? 'draft';

        $this->posts->create($title, $slug, $preview, $content, $status);

        return $this->responseFactory->redirect('/blog/manage');
    }

    public function showEdit(string $id): Response
    {
        if ($response = $this->adminMiddleware->handle()) return $response;

        $post = $this->posts->findById((int) $id);

        if (!$post) {
            return $this->responseFactory->view('404.html.twig');
        }

        return $this->responseFactory->view('blog/edit.html.twig', [
            'post' => $post,
            'active' => 'blog'
        ]);
    }

    public function update(string $id): Response
    {
        if ($response = $this->adminMiddleware->handle()) return $response;

        $title   = $_POST['title'] ?? '';
        $slug    = $_POST['slug'] ?? '';
        $preview = $_POST['preview'] ?? '';
        $content = $_POST['content'] ?? '';
        $status  = $_POST['status'] ?? 'draft';

        $this->posts->update((int) $id, $title, $slug, $preview, $content, $status);

        return $this->responseFactory->redirect('/blog/manage');
    }

    public function delete(string $id): Response
    {
        if ($response = $this->adminMiddleware->handle()) return $response;

        $this->posts->delete((int) $id);

        return $this->responseFactory->redirect('/blog/manage');
    }
}