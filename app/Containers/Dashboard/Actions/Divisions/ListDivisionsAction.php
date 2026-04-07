<?php

namespace App\Containers\Dashboard\Actions\Divisions;

use App\Containers\InstituteStructure\Models\Division;

class ListDivisionsAction
{
    public function run(array $filters = []): array
    {
        $query = Division::query();

        // Фильтр по статусу (по умолчанию только активные)
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        } elseif (!isset($filters['is_active'])) {
            $query->where('is_active', true);
        }

        // Поиск по названию
        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%')
                ->orWhere('slug', 'like', '%' . $filters['search'] . '%');
        }

        $divisions = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return [
            'divisions' => $divisions,
            'filters' => $filters,
        ];
    }
}
