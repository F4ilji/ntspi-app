<?php

namespace App\Containers\InstituteStructure\Tasks;

use App\Containers\InstituteStructure\Models\Division;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class GetDivisionBySlugTask
{
    public function run(string $slug): Division
    {
        return Cache::remember(CacheKeys::DIVISION_PREFIX->value . $slug, now()->addDay(), function () use ($slug) {
            return Division::query()
                ->with([
                    'workers' => fn ($query) => $query->orderBy('sort', 'asc'),
                    'workers.userDetail',
                    'seo'
                ])
                ->where('is_active', true)
                ->where('slug', $slug)
                ->firstOrFail();
        });
    }
}
