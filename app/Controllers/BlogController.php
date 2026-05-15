<?php

namespace App\Controllers;

use App\Repositories\PostRepositoryInterface;
use Framework\Response;
use Framework\ResponseFactory;

class BlogController
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private PostRepositoryInterface $posts
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
}