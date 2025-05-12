<?php

namespace App\Ship\Middleware;

use App\Containers\AppStructure\Models\Page;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем, существует ли запись для текущего маршрута
        $registeredRoute = Page::where('path', '=', $request->route()->uri)
            ->where('is_registered', '=', true)
            ->first();

        // Если запись не найдена, пропускаем запрос
        if (!$registeredRoute) {
            return $next($request);
        }

        // Если код не 200, возвращаем соответствующий код ошибки
        if ($registeredRoute->code != 200) {
            abort($registeredRoute->code);
        }

        $request->attributes->set('settings_page', $registeredRoute->settings);
        // Если все проверки пройдены, продолжаем выполнение запроса
        return $next($request);
    }
}