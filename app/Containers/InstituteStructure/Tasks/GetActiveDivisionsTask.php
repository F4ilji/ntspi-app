<?php

namespace App\Containers\InstituteStructure\Tasks;

use App\Containers\InstituteStructure\Models\Division;
use App\Containers\InstituteStructure\UI\WEB\Transformers\DivisionResource;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetActiveDivisionsTask
{
    public function run(): AnonymousResourceCollection
    {
        return Cache::remember(CacheKeys::DIVISIONS_PREFIX->value . 'list', now()->addDay(), function () {
            return DivisionResource::collection(
                Division::query()->where('is_active', true)->get()
            );
        });
    }
}
