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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PageController extends Controller
{
    public function render(Request $request, $path)
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

        if (isset($page->section)) {
            $subSectionPages = PageResource::collection($page->section->pages);
            $breadcrumbs = [
                'mainSection' => new ClientBreadcrumbSection($page->section->mainSection),
                'subSection' => new ClientBreadcrumbSubSection($page->section),
                'page' => new ClientBreadcrumbPage($page),
            ];
        } else {
            $subSectionPages = null;
            $breadcrumbs = null;
        }

        $seo = $page->seo ?? null;

        $page = new PageResource($page);

        $error = $page->code;

        if ($page->code != 200) {
            abort($error);
        }

        return Inertia::render('Page', compact('page', 'subSectionPages', 'breadcrumbs', 'seo'));
    }
    public function getRegisteredPages()
    {
        $pages = PageResource::collection(Page::query()
            ->when(request()->input('search'), function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->where('is_registered', true)
            ->where('is_visible', true)
            ->orderBy('id', 'desc')
            ->paginate(request()->input('perPage', 9))
            ->withQueryString());
        $filters = [
            'search' => request()->input('search'),
        ];
        return Inertia::render('AdminPanel/Pages/Registered', compact('pages', 'filters'));
    }
}
