<?php

namespace App\Containers\Dashboard\Actions\Schedules;

use App\Containers\Schedule\Models\Schedule;

class DeleteScheduleAction
{
    public function run(Schedule $schedule): bool
    {
        return $schedule->delete();
    }
}
