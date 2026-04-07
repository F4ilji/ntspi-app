<?php

namespace App\Containers\Dashboard\Actions\PageReferenceLists;

use App\Containers\Widget\Models\PageReferenceList;

class DeletePageReferenceListAction
{
    public function run(PageReferenceList $list): bool
    {
        return $list->delete();
    }
}
