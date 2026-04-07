<?php

namespace App\Containers\Dashboard\Tasks\Stats;

use App\Containers\Article\Models\Post;
use App\Containers\Schedule\Models\Schedule;
use App\Containers\Widget\Models\Slider;

class GetRecentActivityTask
{
    private const RECENT_POSTS_LIMIT = 5;
    private const RECENT_SCHEDULES_LIMIT = 3;
    private const RECENT_SLIDERS_LIMIT = 3;

    public function run(): array
    {
        return [
            'recent_posts' => Post::query()
                ->whereNotNull('user_id')
                ->with(['category', 'author'])
                ->latest()
                ->limit(self::RECENT_POSTS_LIMIT)
                ->get([
                    'id',
                    'title',
                    'slug',
                    'status',
                    'category_id',
                    'user_id',
                    'created_at',
                    'updated_at',
                ]),
            'recent_schedules' => Schedule::query()
                ->with('educationalGroup')
                ->latest()
                ->limit(self::RECENT_SCHEDULES_LIMIT)
                ->get([
                    'id',
                    'file',
                    'educational_group_id',
                    'created_at',
                ]),
            'recent_sliders' => Slider::query()
                ->withCount('slides')
                ->latest()
                ->limit(self::RECENT_SLIDERS_LIMIT)
                ->get([
                    'id',
                    'title',
                    'slug',
                    'created_at',
                ]),
        ];
    }
}
