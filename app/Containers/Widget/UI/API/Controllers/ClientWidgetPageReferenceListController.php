<?php

namespace App\Containers\Widget\UI\API\Controllers;

use App\Containers\Widget\Models\PageReferenceList;
use App\Containers\Widget\UI\API\Transformers\PageReferenceListResource;
use App\Ship\Controllers\Controller;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class ClientWidgetPageReferenceListController extends Controller
{
    public function show(string $slug)
    {
        return Cache::remember(
            CacheKeys::PAGE_REFERENCE_LIST_PREFIX->value . $slug,
            now()->addWeek(), // Кешируем на неделю, так как справочники меняются редко
            function () use ($slug) {
                return new PageReferenceListResource(
                    PageReferenceList::query()
                        ->where('slug', $slug)
                        ->firstOrFail()
                );
            }
        );
    }
}
