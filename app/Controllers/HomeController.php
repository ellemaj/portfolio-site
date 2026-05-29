<?php

namespace App\Controllers;

use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class HomeController
{
    public function __construct(private ResponseFactory $responseFactory) {}

    public function index(Request $request): Response
    {
        return $this->responseFactory->view('index.html.twig', [
            'active' => 'home'
        ]);
    }

    public function profile(Request $request): Response
    {
        return $this->responseFactory->view('profile.html.twig', [
            'active' => 'profile'
        ]);
    }

    public function faq(Request $request): Response
    {
        return $this->responseFactory->view('faq.html.twig', [
            'active' => 'faq'
        ]);
    }

    public function sitemap(Request $request): Response
    {
        return $this->responseFactory->view('sitemap.html.twig', [
            'active' => 'sitemap'
        ]);
    }

    public function commandmaker(Request $request): Response
    {
        return $this->responseFactory->view('commandmaker.html.twig', [
            'active' => 'commandmaker'
        ]);
    }
}