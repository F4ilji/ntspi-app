<?php

namespace App\Containers\Dashboard\Actions\Departments;

use App\Containers\InstituteStructure\Models\Department;

class ListDepartmentTeachersAction
{
    public function run(Department $department, array $filters = []): array
    {
        $query = $department->teachers();

        // Поиск по имени
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Фильтр по должности
        if (!empty($filters['position'])) {
            $query->where('teaching_position', 'like', '%' . $filters['position'] . '%');
        }

        $teachers = $query->orderBy('teachers_departments.sort')->paginate(20)->withQueryString();

        return [
            'teachers' => $teachers,
            'filters' => $filters,
        ];
    }
}
