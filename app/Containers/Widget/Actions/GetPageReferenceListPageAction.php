<?php

namespace App\Containers\Widget\Actions;

use App\Containers\Widget\Tasks\FindPageReferenceListBySlugTask;
use App\Containers\Widget\UI\API\Transformers\PageReferenceListResource;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class GetPageReferenceListPageAction
{
    public function __construct(private FindPageReferenceListBySlugTask $findPageReferenceListBySlugTask)
    {
    }

    public function run(string $slug): PageReferenceListResource
    {
        return Cache::remember(
            CacheKeys::PAGE_REFERENCE_LIST_PREFIX->value . $slug,
            now()->addWeek(),
            function () use ($slug) {
                return new PageReferenceListResource(
                    $this->findPageReferenceListBySlugTask->run($slug)
                );
            }
        );
    }
}
