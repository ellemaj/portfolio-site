<?php

namespace App;

use App\Controllers\HomeController;
use App\Controllers\BlogController;
use App\Controllers\UserController;

use App\Repositories\PostRepository;
use App\Repositories\PostRepositoryInterface;

use Exception;
use Framework\Database;
use Framework\ResponseFactory;
use Framework\ServiceContainer;
use Framework\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @throws Exception
     */
    public function register(ServiceContainer $container): void
    {
        $responseFactory = $container->get(ResponseFactory::class);

        $database = $container->get(Database::class);

        $postRepository = new PostRepository($database);
        $container->set(PostRepositoryInterface::class, $postRepository);

        $homeController = new HomeController($responseFactory);
        $container->set(HomeController::class, $homeController);

        $blogController = new BlogController($responseFactory, $container->get(PostRepositoryInterface::class));
        $container->set(BlogController::class, $blogController);

        $userController = new UserController($responseFactory, $database);
        $container->set(UserController::class, $userController);

    }
}
