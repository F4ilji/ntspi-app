<?php

namespace App\Ship\Middleware;

use App\Ship\Abstracts\Middleware\TrimStrings as AbstractMiddleware;

class TrimStrings extends AbstractMiddleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array<int, string>
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}
