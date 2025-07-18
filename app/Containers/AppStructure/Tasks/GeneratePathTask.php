<?php

namespace App\Containers\AppStructure\Tasks;

use Illuminate\Support\Facades\Route;

class GeneratePathTask
{
    public function run(?string $routeName): string|null
    {
        if ($routeName === 'page.view') {
            return request()->path();
        }

        try {
            $route = Route::getRoutes()->getByName($routeName);

            // Если у маршрута есть обязательные параметры без значений по умолчанию, возвращаем null
            if ($route && count($route->parameterNames()) > 0) {
                return null;
            }

            $routeUrl = route($routeName);
            return ltrim(parse_url($routeUrl, PHP_URL_PATH), '/');
        } catch (\Exception $e) {
            return null;
        }
    }
}
