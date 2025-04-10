<?php

namespace App\Observers;

use App\Models\MainSlider;
use App\Services\App\Cache\MainSliderCacheService;

class MainSliderObserver
{

    protected MainSliderCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = new MainSliderCacheService();
    }
    /**
     * Handle the MainSlider "created" event.
     */
    public function created(MainSlider $mainSlider): void
    {


        // Устанавливаем сортировку для новой записи
        $mainSlider->sort = 1;
        $mainSlider->save();

        // Обновляем сортировку для всех остальных записей
        $this->updateSortOrder($mainSlider->id);

        $this->cacheService->clearAllCacheByModel();
    }

    /**
     * Handle the MainSlider "updated" event.
     */
    public function updated(MainSlider $mainSlider): void
    {
        $this->cacheService->clearAllCacheByModel();
    }

    /**
     * Handle the MainSlider "deleted" event.
     */
    public function deleted(MainSlider $mainSlider): void
    {
        $this->cacheService->clearAllCacheByModel();
    }

    /**
     * Handle the MainSlider "restored" event.
     */
    public function restored(MainSlider $mainSlider): void
    {
        //
    }

    /**
     * Handle the MainSlider "force deleted" event.
     */
    public function forceDeleted(MainSlider $mainSlider): void
    {
        //
    }

    protected function updateSortOrder($id): void
    {
        // Получаем все записи, отсортированные по текущему значению sort
        $slides = MainSlider::orderBy('sort', 'asc')->where('id', '!=', $id)->get();

        if ($slides->count() > 0) {
            foreach ($slides as $index => $slide) {
                $slide->sort = $index + 2; // Начинаем с 1
                $slide->save();
            }
        }
    }
}
