<?php

namespace App\Containers\AppStructure\Tasks;

class GetIndexRouteNameTask
{
    public function run(string $routeName = null): ?string
    {
        if ($routeName === null) {
            return null;
        }

        $parts = explode('.', $routeName);

        // Если в маршруте нет точек или он уже заканчивается на index
        if (count($parts) <= 1 || end($parts) === 'index') {
            return $routeName;
        }

        // Заменяем последнюю часть на index
        $parts[count($parts) - 1] = 'index';

        return implode('.', $parts);
    }
}
