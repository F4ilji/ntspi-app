<?php

namespace App\Containers\Dashboard\Actions\PageReferenceLists;

use App\Containers\Widget\Models\PageReferenceList;
use Illuminate\Support\Str;

class CreatePageReferenceListAction
{
    public function run(array $data): PageReferenceList
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['is_active'] = $data['is_active'] ?? true;
        $data['content'] = $data['content'] ?? [];

        return PageReferenceList::create($data);
    }
}
