<?php

namespace App;

use App\Controllers\HomeController;
use App\Controllers\BlogController;
use App\Controllers\UserController;
use App\Controllers\DashboardController;
use App\Controllers\ProfileController;

use App\Middleware\AdminMiddleware;

use App\Repositories\PostRepository;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\CourseRepository;
use App\Repositories\CourseRepositoryInterface;
use App\Repositories\ProfileRepository;
use App\Repositories\ProfileRepositoryInterface;

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

        // Middleware
        $adminMiddleware = new AdminMiddleware($responseFactory);

        // Repositories
        $postRepository = new PostRepository($database);
        $container->set(PostRepositoryInterface::class, $postRepository);

        $userRepository = new UserRepository($database);
        $container->set(UserRepositoryInterface::class, $userRepository);

        $courseRepository = new CourseRepository($database);
        $container->set(CourseRepositoryInterface::class, $courseRepository);

        $profileRepository = new ProfileRepository($database);
        $container->set(ProfileRepositoryInterface::class, $profileRepository);

        // Controllers
        $homeController = new HomeController($responseFactory);
        $container->set(HomeController::class, $homeController);

        $blogController = new BlogController($responseFactory, $container->get(PostRepositoryInterface::class), $adminMiddleware);
        $container->set(BlogController::class, $blogController);

        $userController = new UserController($responseFactory, $container->get(UserRepositoryInterface::class));
        $container->set(UserController::class, $userController);

        $dashboardController = new DashboardController($responseFactory, $container->get(CourseRepositoryInterface::class), $adminMiddleware);
        $container->set(DashboardController::class, $dashboardController);

        $profileController = new ProfileController($responseFactory, $container->get(ProfileRepositoryInterface::class), $adminMiddleware);
        $container->set(ProfileController::class, $profileController);
    }
}