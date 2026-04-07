<?php

namespace App\Containers\Dashboard\Actions\PageReferenceLists;

use App\Containers\Widget\Models\PageReferenceList;

class UpdatePageReferenceListAction
{
    public function run(PageReferenceList $list, array $data): PageReferenceList
    {
        $list->update($data);

        return $list;
    }
}
