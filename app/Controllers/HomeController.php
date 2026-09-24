<?php

namespace App\Controllers;

use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class HomeController
{
    public function __construct(private ResponseFactory $responseFactory)
    {
    }

    public function index(Request $request): Response
    {
        return $this->responseFactory->view('index.html.twig', [
            'active' => 'home'
        ]);
    }

    public function commandmaker(Request $request): Response
    {
        return $this->responseFactory->view('commandmaker.html.twig', [
            'active' => 'commandmaker'
        ]);
    }
}
