<?php

namespace App\Http\Middleware;

use App\Containers\AppStructure\Models\Page;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            Log::warning('AccessCheck: abort', [
                'uri' => $request->route()->uri,
                'method' => $request->method(),
                'page_id' => $registeredRoute->id,
                'code' => $registeredRoute->code,
                'path' => $registeredRoute->path,
            ]);
            abort($registeredRoute->code);
        }

        $request->attributes->set('settings_page', $registeredRoute->settings);
        // Если все проверки пройдены, продолжаем выполнение запроса
        return $next($request);
    }
}
