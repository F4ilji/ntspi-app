<?php

namespace App\Http\Controllers;

use App\Enums\CacheKeys;
use App\Http\Resources\ClientPageReferenceListResource;
use App\Models\PageReferenceList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ClientWidgetPageReferenceListController extends Controller
{
    public function show(string $slug)
    {
        return Cache::remember(
            CacheKeys::PAGE_REFERENCE_LIST_PREFIX->value . $slug,
            now()->addWeek(), // Кешируем на неделю, так как справочники меняются редко
            function () use ($slug) {
                return new ClientPageReferenceListResource(
                    PageReferenceList::query()
                        ->where('slug', $slug)
                        ->first()
                );
            }
        );
    }
}
