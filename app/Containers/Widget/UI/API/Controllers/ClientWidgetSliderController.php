<?php

namespace App\Containers\Widget\UI\API\Controllers;

use App\Containers\Widget\Models\Slider;
use App\Ship\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class ClientWidgetSliderController extends Controller
{
    public function show(string $slug): ?object
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
