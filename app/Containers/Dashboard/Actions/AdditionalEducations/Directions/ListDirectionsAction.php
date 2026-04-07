<?php

namespace App\Containers\Dashboard\Actions\AdditionalEducations\Directions;

use App\Containers\AdditionalEducation\Models\DirectionAdditionalEducation;

class ListDirectionsAction
{
    public function run(array $filters = []): array
    {
        $query = DirectionAdditionalEducation::query();

        // Фильтр по активности
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        // Поиск по названию
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('title', 'like', '%' . $search . '%');
        }

        $directions = $query->orderBy('title')->paginate(20)->withQueryString();

        return [
            'directions' => $directions,
            'filters' => $filters,
        ];
    }
}
