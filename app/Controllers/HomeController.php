<?php

namespace App\Controllers;

use Exception;
use Framework\Response;
use Framework\ResponseFactory;

class HomeController
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
        return $this->responseFactory->view('index.html.twig', [
            'active' => 'home'
        ]);
    }

    /**
     * @throws Exception
     */
    public function profile(): Response
    {
        return $this->responseFactory->view('profile.html.twig', [
            'active' => 'profile'
        ]);
    }

    /**
     * @throws Exception
     */
    public function dashboard(): Response
    {
        return $this->responseFactory->view('dashboard.html.twig', [
            'active' => 'dashboard'
        ]);
    }

    /**
     * @throws Exception
     */
    public function faq(): Response
    {
        return $this->responseFactory->view('faq.html.twig', [
            'active' => 'faq'
        ]);
    }

    /**
     * @throws Exception
     */
    public function sitemap(): Response
    {
        return $this->responseFactory->view('sitemap.html.twig', [
            'active' => 'sitemap'
        ]);
    }

    /**
     * @throws Exception
     */
    public function commandmaker(): Response
    {
        return $this->responseFactory->view('commandmaker.html.twig', [
            'active' => 'commandmaker'
        ]);
    }
}
