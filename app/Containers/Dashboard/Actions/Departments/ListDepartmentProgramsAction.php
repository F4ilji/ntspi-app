<?php

namespace App\Containers\Dashboard\Actions\Departments;

use App\Containers\InstituteStructure\Models\Department;
use App\Services\App\Cache\DepartmentCacheService;

class ListDepartmentProgramsAction
{
    public function run(Department $department, array $filters = []): array
    {
        $query = $department->programs();

        // Фильтр по статусу
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Поиск по названию
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        $programs = $query->orderBy('name')->paginate(20)->withQueryString();

        return [
            'programs' => $programs,
            'filters' => $filters,
        ];
    }
}
