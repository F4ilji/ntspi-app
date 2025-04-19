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
    public function getSeoForCurrentPage(): array|null
    {
        $path = $this->getCurrentPath();

        $page = Cache::remember(
            CacheKeys::PAGE_PREFIX->value . $path,
            now()->addHours(1),
            fn() => Page::where('path', $path)->first()
        );
        if ($page) {
            return $page->seo->toArray();
        }

        return null;
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