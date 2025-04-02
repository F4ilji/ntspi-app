<?php

namespace App\Services\App\Seo;

use App\Enums\CacheKeys;
use App\Models\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class SeoPageProvider
{
    public function getSeoForModel(Model $model): ?array
    {
        return $model->seo?->toArray();
    }
    public function getSeoForCurrentPage(): ?array
    {
        $path = $this->getCurrentPath();

        $page = Cache::remember(
            CacheKeys::PAGE_PREFIX->value . $path,
            now()->addHours(1),
            fn() => Page::where('path', $path)->first()
        );

        return $page->seo?->toArray(); // Предполагается, что у модели Page есть поле `seo` (JSON или массив)
    }

    private function getCurrentPath(): string
    {
        if (Route::currentRouteName() === 'page.view') {
            return request()->path();
        }

        $routeUrl = route(Route::currentRouteName());
        return ltrim(parse_url($routeUrl, PHP_URL_PATH), '/');
    }
}