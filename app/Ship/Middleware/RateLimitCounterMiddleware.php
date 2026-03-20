<?php

namespace App\Ship\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RateLimitCounterMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $key = 'rate_limit:' . $ip;
        $maxAttempts = 5; // Максимальное количество попыток
        $decayMinutes = 1; // Время блокировки в минутах

        // Получаем текущее количество попыток
        $attempts = Cache::get($key, 0);

        if ($attempts >= $maxAttempts) {
            return response()->json(['message' => 'Слишком много запросов, пожалуйста, попробуйте позже.'], 429);
        }

        // Увеличиваем количество попыток
        Cache::put($key, $attempts + 1, $decayMinutes * 60);

        return $next($request);
    }
}