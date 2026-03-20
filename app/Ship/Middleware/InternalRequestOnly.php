<?php

namespace App\Ship\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalRequestOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $userAgent = $request->header('User-Agent');

        // Проверяем, что User-Agent содержит ключевые слова, характерные для браузеров
        if (!preg_match('/Mozilla|Chrome|Safari|Firefox|Edge/i', $userAgent)) {
            return response('Access denied. This route is available only from a web browser.', 403);
        }

        return $next($request);
    }
}
