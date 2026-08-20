<?php

namespace App\Containers\Dashboard\Tasks\Schedules;

use App\Containers\Schedule\Models\Schedule;

class GetFilteredScheduleIdsTask
{
    public function run(array $filters = []): array
    {
        $query = Schedule::query();

        if (!empty($filters['educational_group_id'])) {
            $query->where('educational_group_id', $filters['educational_group_id']);
        }

        if (!empty($filters['education_form_id'])) {
            $query->whereHas('educationalGroup', function ($q) use ($filters) {
                $q->where('education_form_id', $filters['education_form_id']);
            });
        }

        if (!empty($filters['search'])) {
            $query->whereHas('educationalGroup', function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->pluck('id')->toArray();
    }
}
