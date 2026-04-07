<?php

namespace App\Containers\Dashboard\Actions\Schedules;

use App\Containers\Schedule\Models\Schedule;

class UpdateScheduleAction
{
    public function run(Schedule $schedule, array $data): Schedule
    {
        $schedule->update($data);
        return $schedule->fresh();
    }
}
