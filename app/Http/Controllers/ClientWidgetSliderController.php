<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Slider;

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
                ->first();

            return $slider ?: null;
        });
    }
}
