<?php

namespace App\Containers\Widget\Tasks;

use App\Containers\Widget\Models\Slider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class FindSliderBySlugTask
{
    public function run(string $slug): ?object
    {
        $cacheKey = 'slider_' . $slug;

        return Cache::remember($cacheKey, now()->addHour(), function () use ($slug) {
            $now = Carbon::now(); // Получаем текущее время один раз

            $slider = Slider::query()
                ->where('slug', $slug)
                ->where('is_active', true)
                ->with(['slides' => function($query) use ($now) {
                    $query->where('is_active', true)
                        ->where('start_time', '<=', $now)
                        ->where('end_time', '>=', $now);
                }])
                ->firstOrFail();

            return $slider ?: null;
        });
    }
}