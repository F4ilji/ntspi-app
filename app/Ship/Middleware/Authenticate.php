<?php

namespace App\Ship\Middleware;

use App\Ship\Abstracts\Middleware\Authenticate as AbstractMiddleware;

class Authenticate extends AbstractMiddleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return '/';
        }
    }
}
