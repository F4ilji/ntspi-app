<?php

namespace App\Containers\Widget\Tasks;

use App\Containers\AppStructure\Models\Page;
use Illuminate\Support\Facades\Cache;

class FindPageByIdTask
{
    public function run(int $id)
    {
        $cacheKey = 'page_' . md5($id);

        return Cache::remember($cacheKey, now()->addHours(1), function () use ($id) {
            return Page::where('id', '=', $id)
                ->with('section.pages.section', 'section.mainSection')
                ->firstOrFail();
        });
    }
}
