<?php

namespace App\Containers\AppStructure\Actions;

use App\Containers\AppStructure\Models\Page;
use App\Containers\AppStructure\Tasks\FindPageByPathTask;
use App\Containers\AppStructure\UI\WEB\Transformers\PageResource;
use App\Ship\Contracts\SeoServiceInterface;
use Inertia\Response;

class RenderPageAction
{
    public function __construct(readonly SeoServiceInterface $seoPageProvider, readonly FindPageByPathTask $findPageByPathTask){}

    public function run(string $path): Response
    {
        $page = $this->findPageByPathTask->run($path);
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
}
