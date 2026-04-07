<?php

namespace App\Containers\Dashboard\Actions\Departments;

use App\Containers\InstituteStructure\Models\Department;
use App\Containers\User\Models\User;

class ListDepartmentWorkersAction
{
    public function run(Department $department, array $filters = []): array
    {
        $query = $department->workers();

        // Поиск по имени
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Фильтр по должности
        if (!empty($filters['position'])) {
            $query->where('position', 'like', '%' . $filters['position'] . '%');
        }

        $workers = $query->orderBy('workers_departments.sort')->paginate(20)->withQueryString();

        return [
            'workers' => $workers,
            'filters' => $filters,
        ];
    }
}
