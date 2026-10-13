<?php

// Security headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Autoload dependencies and classes
require __DIR__ . '/../vendor/autoload.php';

// Load .env file for environments that don't inject variables (e.g. shared hosting)
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
                continue;
            }
            $parts = explode('=', $line, 2);
            $key = trim($parts[0]);
            $value = trim($parts[1], " \t\n\r\0\x0B\"'");
            if ($key !== '') {
                $_ENV[$key] = $value;
            }
        }
    }
}

session_start();

use App\RouteProvider;
use App\ServiceProvider;
use Framework\Kernel;
use Framework\Request;

$config = [
    'APP_ENV'    => (string)($_ENV['APP_ENV'] ?? 'development'),
    'VIEWS_PATH' => (string)($_ENV['VIEWS_PATH'] ?? 'app/views'),
];

// Initialize the Kernel with configuration
$kernel = new Kernel($config);

$kernel->registerServices(new ServiceProvider());

// Define routes
$kernel->registerRoutes(new RouteProvider());

// Get Request data from the global variables
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Extract the path from the URL
$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (!is_string($urlPath)) {
    $urlPath = '/';
}

// Get query (GET) parameters
$queryParams = $_GET;

// Get POST data
$postData = $_POST;

// Create the Request object
$request = new Request($method, $urlPath, $queryParams, $postData);

// Handle the request and get the response
$response = $kernel->handle($request);

// Send the response to the client (HEAD must omit the body, RFC 9110 §9.3.2)
$response->echo($method !== 'HEAD');
