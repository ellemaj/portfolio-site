<?php

namespace App;

use App\Controllers\HomeController;
use App\Controllers\BlogController;
use App\Controllers\UserController;

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
        $router->addRoute('GET', '/blog/{slug}', [$blogController, 'show']);

        $userController = $container->get(UserController::class);
        $router->addRoute('GET', '/register', [$userController, 'showRegister']);
        $router->addRoute('POST', '/register', [$userController, 'register']);

        $router->addRoute('GET', '/login', [$userController, 'showLogin']);
        $router->addRoute('POST', '/login', [$userController, 'login']);

        $router->addRoute('GET', '/logout', [$userController, 'logout']);

        $router->addRoute('GET', '/overview', [$userController, 'overview']);
    }
}
