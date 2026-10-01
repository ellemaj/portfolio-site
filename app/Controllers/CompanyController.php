<?php

namespace App\Controllers;

use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class CompanyController
{
    public function __construct(private ResponseFactory $responseFactory)
    {
    }

    public function index(Request $request): Response
    {
        return $this->responseFactory->view('company.html.twig', [
            'active' => 'company'
        ]);
    }
}
