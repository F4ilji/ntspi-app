<?php

namespace App\Containers\Dashboard\Actions\PageReferenceLists;

use App\Containers\Widget\Models\PageReferenceList;

class ListPageReferenceListsAction
{
    public function run(array $filters = []): array
    {
        $query = PageReferenceList::query();

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        $lists = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return [
            'lists' => $lists,
            'filters' => $filters,
        ];
    }
}
