<?php

namespace App\Controllers;

use App\Repositories\PostRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class SitemapController
{
    /** @var array<string, string> */
    private const STATIC_PATHS = [
        '/' => 'Home',
        '/commandmaker' => 'Commandmaker',
        '/profile' => 'Profile',
        '/blog' => 'Blog',
    ];

    public function __construct(
        private ResponseFactory $responseFactory,
        private PostRepositoryInterface $postRepository
    ) {
    }

    public function index(Request $request): Response
    {
        $baseUrl = $this->baseUrl();

        $urls = [];
        foreach (array_keys(self::STATIC_PATHS) as $path) {
            $urls[] = ['loc' => $baseUrl . $path];
        }

        foreach ($this->postRepository->findAllPublished() as $post) {
            $urls[] = [
                'loc' => $baseUrl . '/blog/' . $post->slug,
                'lastmod' => date('c', (int)$post->publication_date),
            ];
        }

        return $this->responseFactory->xml($this->buildXml($urls));
    }

    public function html(Request $request): Response
    {
        $pages = [];
        foreach (self::STATIC_PATHS as $path => $label) {
            $pages[] = ['path' => $path, 'label' => $label];
        }

        $posts = [];
        foreach ($this->postRepository->findAllPublished() as $post) {
            $posts[] = ['path' => '/blog/' . $post->slug, 'title' => $post->title];
        }

        return $this->responseFactory->view('sitemap.html.twig', [
            'pages' => $pages,
            'posts' => $posts,
            'active' => 'sitemap',
        ]);
    }

    private function baseUrl(): string
    {
        return rtrim((string)($_ENV['APP_URL'] ?? ''), '/');
    }

    /**
     * @param array<int, array{loc: string, lastmod?: string}> $urls
     */
    private function buildXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1) . '</loc>' . "\n";
            if (isset($url['lastmod'])) {
                $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
            }
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';
        return $xml;
    }
}
