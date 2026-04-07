<?php

namespace App\Containers\Dashboard\Actions\MainSections;

use App\Containers\AppStructure\Models\MainSection;

class ListMainSectionsAction
{
    public function run(array $filters): array
    {
        $query = MainSection::query()->with('subSections');

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        $mainSections = $query->orderBy('sort')->paginate(15);

        return [
            'mainSections' => $mainSections,
        ];
    }
}
