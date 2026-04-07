<?php

namespace App\Containers\Dashboard\Actions\Schedules;

use App\Containers\Schedule\Models\Schedule;

class CreateScheduleAction
{
    public function run(array $data): Schedule
    {
        return Schedule::create($data);
    }
}
