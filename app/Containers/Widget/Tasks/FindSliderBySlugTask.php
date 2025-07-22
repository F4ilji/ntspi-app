<?php

namespace App\Containers\Widget\Tasks;

use App\Containers\Widget\Models\Slider;
use Illuminate\Support\Facades\Cache;

class FindSliderBySlugTask
{
    public function run(string $slug): ?object
    {
        $cacheKey = 'slider_' . $slug;

        return Cache::remember($cacheKey, now()->addHour(), function () use ($slug) {
            $slider = Slider::query()
                ->where('slug', $slug)
                ->where('is_active', true)
                ->with(['slides' => function($query) {
                    $query->where('is_active', true);
                }])
                ->firstOrFail();

            return $slider ?: null;
        });
    }
}
