<?php

namespace App\Containers\Dashboard\Actions\DirectionStudies;

use App\Containers\Education\Models\DirectionStudy;
use App\Ship\Enums\Education\LevelEducational;

class ListDirectionStudiesAction
{
    public function run(array $filters = []): array
    {
        $query = DirectionStudy::query();

        // Фильтр по уровню образования
        if (!empty($filters['lvl_edu'])) {
            $query->where('lvl_edu', $filters['lvl_edu']);
        }

        // Поиск по коду или названию
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        $directions = $query->orderBy('code')->paginate(20)->withQueryString();

        return [
            'directions' => $directions,
            'filters' => $filters,
            'educationLevels' => array_map(fn($level) => [
                'value' => $level->value,
                'label' => $level->getLabel(),
                'color' => $level->getColor(),
            ], LevelEducational::cases()),
        ];
    }
}
