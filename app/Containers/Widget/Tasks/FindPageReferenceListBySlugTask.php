<?php

namespace App\Containers\Widget\Tasks;

use App\Containers\Widget\Models\PageReferenceList;
use App\Ship\Exceptions\NotFoundException;

class FindPageReferenceListBySlugTask
{
    public function run(string $slug): PageReferenceList
    {
        $pageReferenceList = PageReferenceList::query()
            ->where('slug', $slug)
            ->firstOrFail();

        return $pageReferenceList;
    }
}
