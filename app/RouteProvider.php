<?php

namespace App;

use App\Controllers\HomeController;
use App\Controllers\BlogController;
use Framework\Router;
use Framework\RouteProviderInterface;
use Framework\ServiceContainer;
use Exception;

class RouteProvider implements RouteProviderInterface
{
    /**
     * @throws Exception
     */
    public function register(Router $router, ServiceContainer $container): void
    {
        $homeController = $container->get(HomeController::class);
        $router->addRoute('GET', '/', [$homeController, "index"]);
        $router->addRoute('GET', '/profile', [$homeController, "profile"]);
        $router->addRoute('GET', '/dashboard', [$homeController, "dashboard"]);
        $router->addRoute('GET', '/faq', [$homeController, "faq"]);
        
        $router->addRoute('GET', '/sitemap', [$homeController, "sitemap"]);
        $router->addRoute('GET', '/commandmaker', [$homeController, "commandmaker"]);

        $blogController = $container->get(BlogController::class);
        $router->addRoute('GET', '/blog', [$blogController, "index"]);
        $router->addRoute('GET', '/blog/post-feedback', [$blogController, "feedback"]);
        $router->addRoute('GET', '/blog/post-ict', [$blogController, "ict"]);
        $router->addRoute('GET', '/blog/post-programming_experience', [$blogController, "programming_experience"]);
        $router->addRoute('GET', '/blog/post-studiekeuze', [$blogController, "studiekeuze"]);
        $router->addRoute('GET', '/blog/post-swot_analysis', [$blogController, "analysis"]);
        $router->addRoute('GET', '/blog/post-update', [$blogController, "update"]);
    }
}
