<?php

namespace App\Containers\Dashboard\Actions\AdditionalEducations\Categories;

use App\Containers\AdditionalEducation\Models\AdditionalEducationCategory;
use App\Containers\AdditionalEducation\Models\DirectionAdditionalEducation;

class ListCategoriesAction
{
    public function run(array $filters = []): array
    {
        $query = AdditionalEducationCategory::with(['direction']);

        // Фильтр по направлению
        if (!empty($filters['direction_id'])) {
            $query->where('dir_addit_educat_id', $filters['direction_id']);
        }

        // Фильтр по активности
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        // Поиск по названию
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('title', 'like', '%' . $search . '%');
        }

        $categories = $query->orderBy('title')->paginate(20)->withQueryString();

        return [
            'categories' => $categories,
            'filters' => $filters,
            'directions' => DirectionAdditionalEducation::where('is_active', true)
                ->orderBy('title')
                ->get(['id', 'title']),
        ];
    }
}
