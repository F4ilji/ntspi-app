<?php

namespace App\Containers\Schedule\Loaders;

class MiddlewareLoader
{
    /**
     * @var array
     */
    public array $middleware = [];

    /**
     * @var array
     */
    public array $middlewareGroups = [];

    /**
     * @var array
     */
    public array $routeMiddleware = [];

    /**
     * @var array
     */
    public array $middlewarePriority = [];
}
