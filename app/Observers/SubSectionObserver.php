<?php

namespace App\Observers;

use App\Containers\AppStructure\Models\SubSection;
use Illuminate\Support\Facades\Cache;

class SubSectionObserver
{
    public function created(SubSection $subSection): void
    {
        Cache::forget('navigation');
    }
    /**
     * Очистка кеша при обновлении SubSection.
     *
     * @param  SubSection  $subSection
     * @return void
     */
    public function updated(SubSection $subSection)
    {
        Cache::forget('navigation');
    }

    /**
     * Очистка кеша при удалении SubSection.
     *
     * @param  SubSection  $subSection
     * @return void
     */
    public function deleted(SubSection $subSection)
    {
        Cache::forget('navigation');
    }
}