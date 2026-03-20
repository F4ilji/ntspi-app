<?php

namespace App\Containers\AppStructure\Tasks;

use App\Containers\AppStructure\Models\Page;

class GetPagesForSitemapTask
{
    public function run()
    {
        return Page::query()
            ->where('is_visible', true)
            ->where('code', 200)
            ->where('path', '!=', null)
            ->where('is_url', false)
            ->where('title', '!=', null)
            ->where('searchable', true)
            ->get();
    }
}
