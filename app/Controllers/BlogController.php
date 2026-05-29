<?php

namespace App\Controllers;

use App\Middleware\AdminMiddleware;
use App\Repositories\PostRepositoryInterface;
use App\Models\Post;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class BlogController
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private PostRepositoryInterface $postRepository,
        private AdminMiddleware $adminMiddleware
    ) {}

    public function index(Request $request): Response
    {
        $posts = $this->postRepository->findAllPublished();

        return $this->responseFactory->view('blog/index.html.twig', [
            'posts' => $posts,
            'active' => 'blog'
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $post = $this->postRepository->findBySlug($slug);

        if (!$post) {
            return $this->responseFactory->view('404.html.twig');
        }

        return $this->responseFactory->view('blog/post.html.twig', [
            'post' => $post,
            'active' => 'blog'
        ]);
    }

    public function manage(Request $request): Response
    {
        if ($response = $this->adminMiddleware->handle()) return $response;

        $posts = $this->postRepository->findAll();

        return $this->responseFactory->view('blog/manage.html.twig', [
            'posts' => $posts,
            'active' => 'blog'
        ]);
    }

    public function showCreate(Request $request): Response
    {
        if ($response = $this->adminMiddleware->handle()) return $response;

        return $this->responseFactory->view('blog/create.html.twig', [
            'active' => 'blog'
        ]);
    }

    public function create(Request $request): Response
    {
        if ($response = $this->adminMiddleware->handle()) return $response;

        $title            = $request->get('title');
        $slug             = $request->get('slug');
        $preview          = $request->get('preview');
        $content          = $request->get('content');
        $status           = $request->get('status') ?? 'draft';
        $publication_date = $request->get('publication_date');

        if (!$title || !$slug || !$preview || !$content || !$status) {
            return $this->responseFactory->internalError();
        }

        if ($this->postRepository->findBySlug($slug)) {
            return $this->responseFactory->internalError();
        }

        $post = new Post();
        $post->title            = $title;
        $post->slug             = $slug;
        $post->preview          = $preview;
        $post->content          = $content;
        $post->status           = $status;
        $post->publication_date = $publication_date ? strtotime($publication_date) : time();
        $post->created_at       = time();
        $post->deleted_at       = null;

        $createdPost = $this->postRepository->create($post);

        if (!$createdPost) {
            return $this->responseFactory->internalError();
        }

        return $this->responseFactory->redirect('/blog/manage');
    }

    public function showEdit(Request $request, string $id): Response
    {
        if ($response = $this->adminMiddleware->handle()) return $response;

        $post = $this->postRepository->findById((int) $id);

        if (!$post) {
            return $this->responseFactory->view('404.html.twig');
        }

        return $this->responseFactory->view('blog/edit.html.twig', [
            'post' => $post,
            'active' => 'blog'
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        if ($response = $this->adminMiddleware->handle()) return $response;

        $post = $this->postRepository->findById((int) $id);

        if (!$post) {
            return $this->responseFactory->view('404.html.twig');
        }

        $post->title            = $request->get('title') ?? $post->title;
        $post->slug             = $request->get('slug') ?? $post->slug;
        $post->preview          = $request->get('preview') ?? $post->preview;
        $post->content          = $request->get('content') ?? $post->content;
        $post->status           = $request->get('status') ?? $post->status;
        $post->publication_date = $request->get('publication_date')
            ? strtotime($request->get('publication_date'))
            : $post->publication_date;

        $this->postRepository->update((int) $id, $post);

        return $this->responseFactory->redirect('/blog/manage');
    }

    public function delete(Request $request, string $id): Response
    {
        if ($response = $this->adminMiddleware->handle()) return $response;

        $this->postRepository->delete((int) $id);

        return $this->responseFactory->redirect('/blog/manage');
    }
}