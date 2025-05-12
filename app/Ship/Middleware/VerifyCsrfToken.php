<?php

namespace App\Ship\Middleware;

use App\Ship\Abstracts\Middleware\VerifyCsrfToken as AbstractMiddleware;

class VerifyCsrfToken extends AbstractMiddleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
