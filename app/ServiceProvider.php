<?php

namespace App;

use App\Controllers\HomeController;
use App\Controllers\BlogController;
use App\Controllers\UserController;
use App\Controllers\ProfileController;
use App\Controllers\ApiController;
use App\Controllers\ProjectController;
use App\Controllers\SitemapController;
use App\Middleware\AdminMiddleware;
use App\Repositories\PostRepository;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\ProfileRepository;
use App\Repositories\ProfileRepositoryInterface;
use App\Repositories\ProjectRepository;
use App\Repositories\ProjectRepositoryInterface;
use Exception;
use Framework\Database;
use Framework\ResponseFactory;
use Framework\ServiceContainer;
use Framework\ServiceProviderInterface;
use Framework\Session;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @throws Exception
     */
    public function register(ServiceContainer $container): void
    {
        $responseFactory = $container->get(ResponseFactory::class);
        $database = $container->get(Database::class);
        $session = $container->get(Session::class);

        // Middleware
        $adminMiddleware = new AdminMiddleware($responseFactory);
        $container->set(AdminMiddleware::class, $adminMiddleware);

        // Repositories
        $postRepository = new PostRepository($database);
        $container->set(PostRepositoryInterface::class, $postRepository);

        $userRepository = new UserRepository($database);
        $container->set(UserRepositoryInterface::class, $userRepository);

        $profileRepository = new ProfileRepository($database);
        $container->set(ProfileRepositoryInterface::class, $profileRepository);

        $projectRepository = new ProjectRepository($database);
        $container->set(ProjectRepositoryInterface::class, $projectRepository);

        // Controllers
        $homeController = new HomeController($responseFactory);
        $container->set(HomeController::class, $homeController);

        $blogController = new BlogController($responseFactory, $postRepository);
        $container->set(BlogController::class, $blogController);

        $userController = new UserController(
            $responseFactory,
            $userRepository,
            $session,
            $postRepository,
            $projectRepository
        );
        $container->set(UserController::class, $userController);

        $profileController = new ProfileController($responseFactory, $profileRepository, $projectRepository);
        $container->set(ProfileController::class, $profileController);

        $projectController = new ProjectController($responseFactory, $projectRepository);
        $container->set(ProjectController::class, $projectController);

        $apiController = new ApiController($responseFactory, $postRepository);
        $container->set(ApiController::class, $apiController);

        $sitemapController = new SitemapController($responseFactory, $postRepository);
        $container->set(SitemapController::class, $sitemapController);
    }
}
