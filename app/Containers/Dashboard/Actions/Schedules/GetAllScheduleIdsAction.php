<?php

namespace App\Containers\Dashboard\Actions\Schedules;

use App\Containers\Dashboard\Tasks\Schedules\GetFilteredScheduleIdsTask;

class GetAllScheduleIdsAction
{
    public function __construct(
        private readonly GetFilteredScheduleIdsTask $task,
    ) {}

    public function run(array $filters = []): array
    {
        return $this->task->run($filters);
    }
}
