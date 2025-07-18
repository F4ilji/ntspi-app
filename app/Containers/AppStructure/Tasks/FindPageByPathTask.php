<?php

namespace App\Containers\AppStructure\Tasks;

use App\Containers\AppStructure\Models\Page;
use Illuminate\Support\Facades\Cache;

class FindPageByPathTask
{
    public function run(string $path): ?Page
    {
        $cacheKey = 'page_' . md5($path);

        return Cache::remember($cacheKey, now()->addHours(48), function () use ($path) {
            return Page::where('path', '=', $path)
                ->with('section.pages.section', 'section.mainSection')
                ->first();
        });
    }
}
