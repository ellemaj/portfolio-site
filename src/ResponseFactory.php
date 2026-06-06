<?php

namespace Framework;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

class ResponseFactory
{
    private Environment $twig;
    private Session $session;

    public function __construct(bool $debugMode, string $viewsPath, Session $session)
    {
        $loader = new FilesystemLoader(__DIR__ . '/../' . $viewsPath);
        $twig = new Environment($loader, [
            'debug' => $debugMode,
        ]);

        if ($debugMode) {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        }

        $twig->addGlobal('session', $_SESSION);
        $twig->addGlobal('csrf_token', $session->getCsrfToken());

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

        $this->twig    = $twig;
        $this->session = $session;
    }

    public function createToast(string $type, string $message): static
    {
        $toasts   = $this->session->getAttribute('_toasts') ?? [];

        $toasts[] = [
            'type' => $type,
            'message' => $message
        ];

        $this->session->setAttribute('_toasts', $toasts);
        return $this;
    }

    /**
     * @param array<mixed> $context
     */
    public function view(string $view, array $context = []): Response
    {
        $toasts = $this->session->getAttribute('_toasts') ?? [];
        $this->session->clear('_toasts');

        $response = new Response();
        try {
            $response->responseCode = 200;
            $response->body = $this->twig->render($view, array_merge(
                ['toasts' => $toasts],
                $context
            ));
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
            $response->body = $this->twig->render('errors/404.html.twig');
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
            $response->body = $this->twig->render('errors/500.html.twig');
            return $response;
        } catch (\Exception $e) {
            $response->responseCode = 500;
            $response->body = $e->getMessage();
            return $response;
        }
    }

    public function json(mixed $data, int $statusCode = 200): Response
    {
        $response = new Response();
        $response->responseCode = $statusCode;
        $response->body = json_encode($data) ?: '{}';
        $response->header = "Content-Type: application/json";
        return $response;
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
            $response->body = $this->twig->render('errors/403.html.twig');
            return $response;
        } catch (\Exception $e) {
            $response->responseCode = 500;
            $response->body = $e->getMessage();
            return $response;
        }
    }
}
