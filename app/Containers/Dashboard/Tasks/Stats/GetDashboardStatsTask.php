<?php

namespace App\Containers\Dashboard\Tasks\Stats;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;
use App\Containers\Schedule\Models\EducationalGroup;
use App\Containers\Schedule\Models\Schedule;
use App\Containers\Widget\Models\Slider;
use Illuminate\Support\Facades\DB;

class GetDashboardStatsTask
{
    public function run(): array
    {
        $now = now();
        $weekAgo = $now->clone()->subWeek();

        return [
            'posts' => $this->getPostsStats($weekAgo),
            'schedules' => $this->getSchedulesStats($weekAgo),
            'educational_groups' => $this->getEducationalGroupsStats($weekAgo),
            'sliders' => $this->getSlidersStats($weekAgo),
        ];
    }

    private function getPostsStats($weekAgo): array
    {
        $stats = Post::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as week,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as verification,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as published,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected
        ', [
            $weekAgo,
            PostStatus::VERIFICATION,
            PostStatus::PUBLISHED,
            PostStatus::REJECTED,
        ])->first();

        return [
            'total' => (int) $stats->total,
            'week' => (int) $stats->week,
            'verification' => (int) $stats->verification,
            'published' => (int) $stats->published,
            'rejected' => (int) $stats->rejected,
        ];
    }

    private function getSchedulesStats($weekAgo): array
    {
        $stats = Schedule::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as week
        ', [$weekAgo])->first();

        return [
            'total' => (int) $stats->total,
            'week' => (int) $stats->week,
        ];
    }

    private function getEducationalGroupsStats($weekAgo): array
    {
        $stats = EducationalGroup::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as week
        ', [$weekAgo])->first();

        return [
            'total' => (int) $stats->total,
            'week' => (int) $stats->week,
        ];
    }

    private function getSlidersStats($weekAgo): array
    {
        $stats = Slider::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as week
        ', [$weekAgo])->first();

        return [
            'total' => (int) $stats->total,
            'week' => (int) $stats->week,
        ];
    }
}
