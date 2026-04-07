<?php

namespace App\Containers\Dashboard\Actions\Departments;

use App\Containers\InstituteStructure\Models\Department;

class ListDepartmentsAction
{
    public function run(array $filters = []): array
    {
        $query = Department::query()->with(['faculty']);

        // Фильтр по факультету
        if (isset($filters['faculty_id']) && $filters['faculty_id'] !== '') {
            $query->where('faculty_id', (int) $filters['faculty_id']);
        }

        // Фильтр по статусу
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        // Поиск по названию
        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        $departments = $query->orderBy('title')->paginate(20)->withQueryString();

        return [
            'departments' => $departments,
            'filters' => $filters,
        ];
    }
}
