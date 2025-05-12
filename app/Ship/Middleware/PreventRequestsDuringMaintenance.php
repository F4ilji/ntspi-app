<?php

namespace App\Ship\Middleware;

use App\Ship\Abstracts\Middleware\PreventRequestsDuringMaintenance as AbstractMiddleware;

class PreventRequestsDuringMaintenance extends AbstractMiddleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
