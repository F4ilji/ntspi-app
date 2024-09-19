<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RateLimitCheckMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $key = 'rate_limit:' . $ip;

        // Получаем текущее количество попыток
        $attempts = Cache::get($key, 0);

        if ($attempts >= 5) {
            return response()->json(['message' => 'Слишком много запросов, пожалуйста, попробуйте позже.'], 429);
        }

        // Увеличиваем количество попыток

        return $next($request);
    }
}