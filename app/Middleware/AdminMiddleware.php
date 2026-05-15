<?php

namespace App\Middleware;

class AdminMiddleware
{
    public static function handle(): void
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {

            http_response_code(403);
            exit('Forbidden');

        }
    }
}