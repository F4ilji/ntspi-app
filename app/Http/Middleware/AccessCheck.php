<?php

namespace App\Http\Middleware;

use App\Models\MainChapter;
use App\Models\MainSection;
use App\Models\Page;
use App\Models\RegisteredRoute;
use App\Models\SubChapter;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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



        // Если все проверки пройдены, продолжаем выполнение запроса
        return $next($request);
    }
}