<?php

namespace App\Containers\AppStructure\UI\WEB\Controllers;

use App\Containers\AppStructure\Models\Page;
use App\Containers\AppStructure\UI\WEB\Transformers\PageResource;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Inertia\Response;

class PageController extends Controller
{
    public function __construct(readonly SeoServiceInterface $seoPageProvider){}

    public function render(string $path): Response
    {
        $page = $this->getPageByPath($path);
        $this->handleCheckNotFoundStatusCode($page);
        $subSectionPages = $page->section ? PageResource::collection($page->section->pages) : null;
        $seo = $this->seoPageProvider->getSeoForModel($page);
        $pageResource = new PageResource($page);
        $this->handlePageStatusCode($pageResource);

        return inertia()->render('Page', [
            'page' => $pageResource,
            'subSectionPages' => $subSectionPages,
            'seo' => $seo,
        ]);
    }

    private function handlePageStatusCode(PageResource $pageResource): void
    {
        if ($pageResource->code != 200) {
            abort($pageResource->code);
        }
    }

    private function handleCheckNotFoundStatusCode($page): void
    {
        if ($page === null) {
            abort(404);
        }
    }

    public function getPageByPath(string $path): ?Page
    {
        $cacheKey = 'page_' . md5($path);

        return Cache::remember($cacheKey, now()->addHours(48), function () use ($path) {
            return Page::where('path', '=', $path)
                ->with('section.pages.section', 'section.mainSection')
                ->first();
        });
    }
}
