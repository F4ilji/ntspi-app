<?php

namespace App\Ship\Middleware;

use App\Ship\Abstracts\Middleware\EncryptCookies as AbstractMiddleware;

class EncryptCookies extends AbstractMiddleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
