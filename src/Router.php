<?php

namespace Framework;

use Exception;

class Router
{
    /** @var Route[] */
    public array $routes = [];

    /** @var array<string, callable> */
    private array $middlewares = [];

    private ResponseFactory $responseFactory;

    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function addRoute(string $method, string $path, callable $callback): Route
    {
        $route = new Route($method, $path, $callback);
        $this->routes[] = $route;
        return $route;
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if ($route->matches($request->method, $request->path)) {
                $callback = $route->callback;
                $params = array_filter(
                    $route->routeParameters,
                    'is_string',
                    ARRAY_FILTER_USE_KEY
                );

                $request->routeParameters = $params;

                // Run route-level middleware
                if ($route->middleware) {
                    $middlewareResponse = ($route->middleware)($request);
                    if ($middlewareResponse instanceof Response) {
                        return $middlewareResponse;
                    }
                }

                return $callback($request);
            }
        }

        return $this->responseFactory->notFound();
    }
}