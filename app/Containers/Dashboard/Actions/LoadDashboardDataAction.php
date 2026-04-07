<?php

namespace App\Containers\Dashboard\Actions;

use App\Containers\Dashboard\Tasks\Posts\GetAiPreparedPostsTask;
use App\Containers\Dashboard\Tasks\Stats\GetDashboardStatsTask;
use App\Containers\Dashboard\Tasks\Stats\GetRecentActivityTask;

class LoadDashboardDataAction
{
    public function __construct(
        private readonly GetAiPreparedPostsTask $getAiPreparedPostsTask,
        private readonly GetDashboardStatsTask $getDashboardStatsTask,
        private readonly GetRecentActivityTask $getRecentActivityTask,
    ) {}

    public function run(): array
    {
        return [
            'aiPreparedPosts' => $this->getAiPreparedPostsTask->run(),
            'stats' => $this->getDashboardStatsTask->run(),
            'recentActivity' => $this->getRecentActivityTask->run(),
        ];
    }
}
