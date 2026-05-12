<?php

namespace App\Controllers;

use Exception;
use Framework\Response;
use Framework\ResponseFactory;

class BlogController
{
    private ResponseFactory $responseFactory;

    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * @throws Exception
     */
    public function index(): Response
    {
        return $this->responseFactory->view('blog/index.html.twig', [
            'active' => 'blog'
        ]);
    }

    /**
     * @throws Exception
     */
    public function feedback(): Response
    {
        return $this->responseFactory->view('blog/post-feedback.html.twig', [
            'active' => 'blog'
        ]);
    }

    /**
     * @throws Exception
     */
    public function ict(): Response
    {
        return $this->responseFactory->view('blog/post-ict.html.twig', [
            'active' => 'blog'
        ]);
    }

    /**
     * @throws Exception
     */
    public function programming_experience(): Response
    {
        return $this->responseFactory->view('blog/post-programming_experience.html.twig', [
            'active' => 'blog'
        ]);
    }

    /**
     * @throws Exception
     */
    public function studiekeuze(): Response
    {
        return $this->responseFactory->view('blog/post-studiekeuze.html.twig', [
            'active' => 'blog'
        ]);
    }

    /**
     * @throws Exception
     */
    public function analysis(): Response
    {
        return $this->responseFactory->view('blog/post-swot_analysis.html.twig', [
            'active' => 'blog'
        ]);
    }

    /**
     * @throws Exception
     */
    public function update(): Response
    {
        return $this->responseFactory->view('blog/post-update.html.twig', [
            'active' => 'blog'
        ]);
    }
}
