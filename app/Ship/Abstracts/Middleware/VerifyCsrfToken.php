<?php

namespace App\Ship\Abstracts\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as LaravelMiddleware;

abstract class VerifyCsrfToken extends LaravelMiddleware
{

}
