<?php

namespace App\Containers\User\Loaders;

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
