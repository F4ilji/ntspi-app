<?php

namespace App\Containers\AppStructure\UI\WEB\Controllers;

use App\Containers\AppStructure\Models\Page;
use App\Containers\AppStructure\UI\WEB\Transformers\PageResource;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class PageController extends Controller
{
    public function __construct(readonly SeoServiceInterface $seoPageProvider){}

    public function render(string $path): \Inertia\Response
    {
        // Генерируем уникальный ключ для кеширования
        $cacheKey = 'page_' . md5($path);

        // Пытаемся получить данные из кеша
        $page = Cache::remember($cacheKey, now()->addHours(48), function () use ($path) {
            return Page::where('path', '=', $path)
                ->with('section.pages.section', 'section.mainSection')
                ->first();
        });

        if ($page === null) {
            abort(404);
        }
        $subSectionPages = $page->section ? PageResource::collection($page->section->pages) : null;

        $seo = $this->seoPageProvider->getSeoForModel($page);


        $page = new PageResource($page);


        if ($page->code != 200) {
            abort($page->code);
        }

        return inertia()->render('Page', [
            'page' => $page,
            'subSectionPages' => $subSectionPages,
            'seo' => $seo,
        ]);
    }
}
