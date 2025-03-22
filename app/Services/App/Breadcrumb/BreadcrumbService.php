<?php

namespace App\Services\App\Breadcrumb;

use App\Http\Resources\ClientBreadcrumbPage;
use App\Http\Resources\ClientBreadcrumbSection;
use App\Http\Resources\ClientBreadcrumbSubSection;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class BreadcrumbService
{
    public function generateBreadcrumbs($routeName) : array|null
    {
        $path = $this->generatePath($routeName);

        // Кешируем страницу
        $page = Cache::remember('page_' . $path, now()->addHours(1), function () use ($path) {
            return Page::where('path', '=', $path)->with('section.pages.section', 'section.mainSection')->first();
        });

        if (isset($page->section)) {
            $breadcrumbs = [
                'mainSection' => new ClientBreadcrumbSection($page->section->mainSection),
                'subSection' => new ClientBreadcrumbSubSection($page->section),
                'page' => new ClientBreadcrumbPage($page),
            ];
        } else {
            $breadcrumbs = null;
        }

        return $breadcrumbs;
    }

    private function generatePath($routeName) : string
    {
        $routeUrl = route($routeName);
        return ltrim(parse_url($routeUrl, PHP_URL_PATH), '/');
    }
}