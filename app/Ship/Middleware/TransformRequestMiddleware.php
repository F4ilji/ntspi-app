<?php

namespace App\Ship\Middleware;


use App\Ship\Requests\Request;
use Closure;
use Illuminate\Http\Request as LaravelRequest;

class TransformRequestMiddleware
{
    public function handle(LaravelRequest $request, Closure $next)
    {
        // Преобразуем стандартный Request в ваш кастомный
        $customRequest = Request::createFrom($request);

        return $next($customRequest);
    }
}
