<?php

namespace App;

use App\Controllers\HomeController;
use App\Controllers\BlogController;
use App\Controllers\UserController;
use App\Controllers\DashboardController;
use App\Controllers\ProfileController;
use App\Controllers\ApiController;
use App\Middleware\AdminMiddleware;
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
        // Middleware
        $adminMiddleware = $container->get(AdminMiddleware::class);

        $homeController = $container->get(HomeController::class);
        $router->addRoute('GET', '/', [$homeController, "index"]);
        $router->addRoute('GET', '/faq', [$homeController, "faq"]);
        $router->addRoute('GET', '/commandmaker', [$homeController, "commandmaker"]);

        $blogController = $container->get(BlogController::class);
        $router->addRoute('GET', '/blog', [$blogController, "index"]);
        $router->addRoute('GET', '/blog/manage', [$blogController, "manage"])
            ->middleware([$adminMiddleware, 'handle']);
        $router->addRoute('GET', '/blog/create', [$blogController, "showCreate"])
            ->middleware([$adminMiddleware, 'handle']);
        $router->addRoute('POST', '/blog/create', [$blogController, "create"])
            ->middleware([$adminMiddleware, 'handle']);
        $router->addRoute('GET', '/blog/(?<id>\d+)/edit', [$blogController, "showEdit"])
            ->middleware([$adminMiddleware, 'handle']);
        $router->addRoute('POST', '/blog/(?<id>\d+)/update', [$blogController, "update"])
            ->middleware([$adminMiddleware, 'handle']);
        $router->addRoute('POST', '/blog/(?<id>\d+)/delete', [$blogController, "delete"])
            ->middleware([$adminMiddleware, 'handle']);
        $router->addRoute('POST', '/blog/(?<id>\d+)/restore', [$blogController, 'undoDelete'])
            ->middleware([$adminMiddleware, 'handle']);
        $router->addRoute('GET', '/blog/{slug}', [$blogController, "show"]);

        $userController = $container->get(UserController::class);
        $router->addRoute('GET', '/register', [$userController, "showRegister"]);
        $router->addRoute('POST', '/register', [$userController, "register"]);
        $router->addRoute('GET', '/login', [$userController, "showLogin"]);
        $router->addRoute('POST', '/login', [$userController, "login"]);
        $router->addRoute('GET', '/logout', [$userController, "logout"]);
        $router->addRoute('GET', '/overview', [$userController, "overview"])->middleware([$adminMiddleware, 'handle']);

        $dashboardController = $container->get(DashboardController::class);
        $router->addRoute('GET', '/dashboard', [$dashboardController, 'index']);
        $router->addRoute('POST', '/dashboard/grade/update', [$dashboardController, 'updateGrade'])
            ->middleware([$adminMiddleware, 'handle']);

        $profileController = $container->get(ProfileController::class);
        $router->addRoute('GET', '/profile', [$profileController, 'index']);
        $router->addRoute('GET', '/profile/edit', [$profileController, 'edit'])
            ->middleware([$adminMiddleware, 'handle']);
        $router->addRoute('POST', '/profile/update', [$profileController, 'update'])
            ->middleware([$adminMiddleware, 'handle']);

        $apiController = $container->get(ApiController::class);
        $router->addRoute('GET', '/api/posts', [$apiController, 'getPosts']);
        $router->addRoute('GET', '/api/posts/{slug}', [$apiController, 'getPost']);
    }
}
