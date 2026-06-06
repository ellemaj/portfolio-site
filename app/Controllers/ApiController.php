<?php

namespace App\Controllers;

use App\Repositories\PostRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class ApiController
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private PostRepositoryInterface $posts
    ) {
    }

    public function getPosts(Request $request): Response
    {
        $posts = $this->posts->findAllPublished();

        $data = array_map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'preview' => $post->preview,
                'publication_date' => $post->publication_date,
                'status' => $post->status,
            ];
        }, $posts);

        return $this->responseFactory->json($data);
    }

    public function getPost(Request $request): Response
    {
        $slug = $request->routeParameters['slug'] ?? '';
        $post = $this->posts->findBySlug($slug);

        if (!$post) {
            return $this->responseFactory->json(['error' => 'Post niet gevonden']);
        }

        return $this->responseFactory->json([
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'preview' => $post->preview,
            'content' => $post->content,
            'publication_date' => $post->publication_date,
            'status' => $post->status,
        ]);
    }
}
