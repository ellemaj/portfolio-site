<?php

namespace Framework;

use Framework\Session;

class Kernel
{
    private Router $router;

    private ServiceContainer $container;

    private ConfigManager $configManager;

    /**
     * @param string[] $config
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $this->container = new ServiceContainer();

        $this->configManager = new ConfigManager($config);

        +
        $debugMode = $this->configManager->get('APP_ENV') != 'production';
        $viewsPath = $this->configManager->get('VIEWS_PATH');

        $session = new Session();
        $this->container->set(Session::class, $session);

        $responseFactory = new ResponseFactory($debugMode, $viewsPath, $session);
        $this->container->set(ResponseFactory::class, $responseFactory);

        $dbName = $this->configManager->get('APP_DB');
        $database = new Database(__DIR__ . '/../' . $dbName);
        $this->container->set(Database::class, $database);

        $this->router = new Router($responseFactory);
    }

    public function registerRoutes(RouteProviderInterface $routerProvider): void
    {
        $routerProvider->register($this->router, $this->container);
    }

    public function registerServices(ServiceProviderInterface $serviceProvider): void
    {
        $serviceProvider->register($this->container);
    }

    /**
     * Handle the incoming Request and produce a Response.
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        return $this->router->dispatch($request);
    }

    public function getDatabase(): Database
    {
        return $this->container->get(Database::class);
    }
}
