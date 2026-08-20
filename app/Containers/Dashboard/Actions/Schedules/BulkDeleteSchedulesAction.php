<?php

namespace App\Containers\Dashboard\Actions\Schedules;

use App\Containers\Dashboard\Tasks\Schedules\BulkDeleteSchedulesTask;

class BulkDeleteSchedulesAction
{
    public function __construct(
        private readonly BulkDeleteSchedulesTask $bulkDeleteSchedulesTask,
    ) {}

    public function run(array $ids): int
    {
        return $this->bulkDeleteSchedulesTask->run($ids);
    }
}
