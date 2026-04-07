<?php

namespace App\Containers\Dashboard\Actions\Faculties;

use App\Containers\InstituteStructure\Models\Faculty;
use Illuminate\Database\Eloquent\Builder;

class ListFacultyWorkersAction
{
    public function run(Faculty $faculty, array $filters = []): array
    {
        $query = $faculty->workers()
            ->withPivot(['position', 'sort', 'service_email', 'service_phone', 'cabinet'])
            ->whereHas('userDetail');

        // Поиск по ФИО
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Фильтр по должности
        if (!empty($filters['position'])) {
            $query->where('workers_faculties.position', 'like', '%' . $filters['position'] . '%');
        }

        $workers = $query->orderBy('workers_faculties.sort')->paginate(20)->withQueryString();

        return [
            'workers' => $workers,
            'filters' => $filters,
        ];
    }
}
