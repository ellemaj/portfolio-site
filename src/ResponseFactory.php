<?php

namespace Framework;

use Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

class ResponseFactory
{
    private Environment $twig;

    public function __construct(bool $debugMode, string $viewsPath)
    {
        $loader = new FilesystemLoader(__DIR__ . '/../' . $viewsPath);
        $twig = new Environment($loader, [
            'debug' => $debugMode,
        ]);

        if ($debugMode) {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        }

        $twig->addGlobal('session', $_SESSION);

        $twig->addFilter(new TwigFilter('nldate', function (int $timestamp): string {
            $maanden = [
                1 => 'januari', 'februari', 'maart', 'april', 'mei', 'juni',
                'juli', 'augustus', 'september', 'oktober', 'november', 'december'
            ];
            $d = (int) date('j', $timestamp);
            $m = (int) date('n', $timestamp);
            $y = (int) date('Y', $timestamp);
            return $d . ' ' . $maanden[$m] . ' ' . $y;
        }));

        $this->twig = $twig;
    }

    /**
     * @param string $view
     * @param array<mixed> $context
     */
    public function view(string $view, array $context = []): Response
    {
        $response = new Response();
        try {
            $response->responseCode = 200;
            $response->body = $this->twig->render($view, $context);
            return $response;
        } catch (\Exception $e) {
            $response->responseCode = 500;
            $response->body = $e->getMessage();
            return $response;
        }
    }

    public function body(string $txt): Response
    {
        return new Response($txt, 200);
    }

    public function notFound(): Response
    {
        $response = new Response();
        try {
            $response->responseCode = 404;
            $response->body = $this->twig->render('404.html.twig');
            return $response;
        } catch (\Exception $e) {
            $response->responseCode = 500;
            $response->body = $e->getMessage();
            return $response;
        }
    }

    public function internalError(): Response
    {
        $response = new Response();
        try {
            $response->responseCode = 500;
            $response->body = $this->twig->render('500.html.twig');
            return $response;
        } catch (\Exception $e) {
            $response->responseCode = 500;
            $response->body = $e->getMessage();
            return $response;
        }
    }

    public function redirect(string $url): Response
    {
        $response = new Response();
        $response->responseCode = 302;
        $response->header = "Location: " . $url;
        return $response;
    }

    public function forbidden(): Response
    {
        $response = new Response();
        try {
            $response->responseCode = 403;
            $response->body = $this->twig->render('403.html.twig');
            return $response;
        } catch (\Exception $e) {
            $response->responseCode = 500;
            $response->body = $e->getMessage();
            return $response;
        }
    }
}