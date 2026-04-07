<?php

namespace App\Containers\Dashboard\Actions\SubSections;

use App\Containers\AppStructure\Models\SubSection;

class ListSubSectionsAction
{
    public function run(array $filters): array
    {
        $query = SubSection::query()->with(['mainSection', 'pages']);

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['main_section_id'])) {
            $query->where('main_section_id', $filters['main_section_id']);
        }

        $subSections = $query->orderBy('sort')->paginate(15);

        return [
            'subSections' => $subSections,
        ];
    }
}
