<?php

namespace App\Observers;

use App\Containers\Widget\Models\Slide;
use App\Services\App\Cache\SliderCacheService;

class SlideObserver
{
    protected SliderCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = new SliderCacheService();
    }
    /**
     * Handle the MainSlider "created" event.
     */
    public function created(Slide $slide): void
    {
        // Устанавливаем сортировку для новой записи
        $slide->sort = 1;
        $slide->save();

        // Обновляем сортировку для всех остальных записей
        $this->updateSortOrder($slide->id, $slide->slider_id);

        $this->cacheService->clearAllCacheByModel();
    }

    /**
     * Handle the MainSlider "updated" event.
     */
    public function updated(Slide $slide): void
    {
        $this->cacheService->clearAllCacheByModel();
    }

    /**
     * Handle the MainSlider "deleted" event.
     */
    public function deleted(Slide $slide): void
    {
        $this->cacheService->clearAllCacheByModel();
    }

    /**
     * Handle the MainSlider "restored" event.
     */
    public function restored(Slide $slide): void
    {
        //
    }

    /**
     * Handle the MainSlider "force deleted" event.
     */
    public function forceDeleted(Slide $slide): void
    {
        //
    }

    protected function updateSortOrder($id, $slider_id): void
    {
        // Получаем все записи, отсортированные по текущему значению sort
        $slides = Slide::orderBy('sort', 'asc')->where([['slider_id', $slider_id], ['id', '!=', $id]])->get();

        if ($slides->count() > 0) {
            foreach ($slides as $index => $slide) {
                $slide->sort = $index + 2; // Начинаем с 1
                $slide->save();
            }
        }
    }
}
