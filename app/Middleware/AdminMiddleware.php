<?php

namespace App\Middleware;

use Framework\Response;
use Framework\ResponseFactory;

class AdminMiddleware
{
    public function __construct(private ResponseFactory $responseFactory)
    {
    }

    public function handle(): ?Response
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            return $this->responseFactory->forbidden();
        }

        return null;
    }
}
