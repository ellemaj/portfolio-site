<?php

namespace Framework;

class Route
{
    public string $method;

    public string $path;

    /** @var callable */
    public $callback;

    /** @var string[] */
    public array $routeParameters;

    public function __construct(string $method, string $path, callable $callback)
    {
        $this->method = $method;
        $this->path = $path;
        $this->callback = $callback;
    }

    public function matches(string $method, string $path): bool
    {
        if ($this->method !== $method) {
            return false;
        }

        $routePattern = preg_replace(
            '/\{([a-zA-Z0-9_]+)\}/',
            '(?P<$1>[^/]+)',
            $this->path
        );

        if (preg_match(';^' . $routePattern . '$;', $path, $matches)) {
            $this->routeParameters = $matches;
            return true;
        }

        return false;
    }
}