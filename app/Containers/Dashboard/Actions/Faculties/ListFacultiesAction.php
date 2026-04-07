<?php

namespace App\Containers\Dashboard\Actions\Faculties;

use App\Containers\InstituteStructure\Models\Faculty;

class ListFacultiesAction
{
    public function run(array $filters = []): array
    {
        $query = Faculty::query();

        // Фильтр по статусу
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        // Поиск по названию
        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        $faculties = $query->orderBy('title')->paginate(20)->withQueryString();

        return [
            'faculties' => $faculties,
            'filters' => $filters,
        ];
    }
}
