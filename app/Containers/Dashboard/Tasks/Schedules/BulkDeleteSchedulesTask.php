<?php

namespace App\Containers\Dashboard\Tasks\Schedules;

use App\Containers\Schedule\Models\Schedule;

class BulkDeleteSchedulesTask
{
    public function run(array $ids): int
    {
        return Schedule::whereIn('id', $ids)->delete();
    }
}
