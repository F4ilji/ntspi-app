<?php

namespace App\Containers\Widget\Tasks;

use App\Containers\Widget\Models\Slider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon; // Убедитесь, что Carbon импортирован

class FindSliderBySlugTask
{
    public function run(string $slug): ?object
    {
        $cacheKey = 'slider_' . $slug;

        return Cache::remember($cacheKey, now()->addHour(), function () use ($slug) {
            $now = Carbon::now();

            $slider = Slider::query()
                ->where('slug', $slug)
                ->where('is_active', true)
                ->with(['slides' => function($query) use ($now) {
                    $query->where('is_active', true)
                        ->where('start_time', '<=', $now)
                        // Добавляем условие для end_time
                        ->where(function ($q) use ($now) {
                            // Показываем слайд, если end_time еще не наступил
                            $q->where('end_time', '>=', $now)
                                // ИЛИ если end_time вообще не указан (NULL)
                                ->orWhereNull('end_time');
                        });
                }])
                ->firstOrFail();

            return $slider ?: null;
        });
    }
}