<?php

namespace App\Containers\Dashboard\Actions\Divisions;

use App\Containers\InstituteStructure\Models\Division;
use Illuminate\Database\Eloquent\Builder;

class ListDivisionWorkersAction
{
    public function run(Division $division, array $filters = []): array
    {
        $query = $division->workers()
            ->withPivot(['administrativePosition', 'sort', 'service_email', 'service_phone', 'cabinet'])
            ->whereHas('userDetail');

        // Поиск по ФИО
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Фильтр по должности
        if (!empty($filters['position'])) {
            $query->where('division_user.administrativePosition', 'like', '%' . $filters['position'] . '%');
        }

        $workers = $query->orderBy('division_user.sort')->paginate(20)->withQueryString();

        return [
            'workers' => $workers,
            'filters' => $filters,
        ];
    }
}
