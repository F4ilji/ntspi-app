<?php

namespace App\Services\App\Breadcrumb;

use App\Http\Resources\ClientBreadcrumbPage;
use App\Http\Resources\ClientBreadcrumbSection;
use App\Http\Resources\ClientBreadcrumbSubSection;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class BreadcrumbService
{
    public function generateBreadcrumbs(): ?array
    {
        $routeName = Route::currentRouteName();


        // Пытаемся найти index-версию маршрута
        $indexRouteName = $this->getIndexRouteName($routeName);

        if ($indexRouteName === null) {
            return null;
        }


        // Используем index-версию, если она существует
        $finalRouteName = Route::has($indexRouteName) ? $indexRouteName : $routeName;



        $path = $this->generatePath($finalRouteName);

        $page = Cache::remember('page_' . $path, now()->addHours(1), function () use ($path) {
            return Page::where('path', $path)
                ->with('section.pages.section', 'section.mainSection')
                ->first();
        });

        if (!$page?->section) {
            return null;
        }

        return [
            'mainSection' => new ClientBreadcrumbSection($page->section->mainSection),
            'subSection' => new ClientBreadcrumbSubSection($page->section),
            'page' => new ClientBreadcrumbPage($page),
        ];
    }

    private function generatePath(string $routeName): string
    {
        if ($routeName === 'page.view') {
            return request()->path();
        }
        $routeUrl = route($routeName);

        return ltrim(parse_url($routeUrl, PHP_URL_PATH), '/');
    }

    private function getIndexRouteName(string $routeName = null): string|null
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