<?php

namespace App\Http\Controllers;

use App\Http\Requests\Page\StoreRequest;
use App\Http\Requests\Page\UpdateRequest;
use App\Http\Resources\ClientBreadcrumbPage;
use App\Http\Resources\ClientBreadcrumbSection;
use App\Http\Resources\ClientBreadcrumbSubSection;
use App\Http\Resources\ClientNavigationResource;
use App\Http\Resources\MainSectionResource;
use App\Http\Resources\PageResource;
use App\Http\Resources\RegisteredPageResource;
use App\Models\MainSection;
use App\Models\Page;
use App\Models\Post;
use App\Services\App\Seo\SeoPageProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PageController extends Controller
{
    public function __construct(readonly SeoPageProvider $seoPageProvider){}

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


        return Inertia::render('Page', compact('page', 'subSectionPages', 'seo'));
    }
}
