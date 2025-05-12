<?php

namespace App\Observers;

use App\Containers\AppStructure\Models\MainSection;
use Illuminate\Support\Facades\Cache;

class MainSectionObserver
{
    public function created(MainSection $mainSection): void
    {
        Cache::forget('navigation');
    }
    /**
     * Очистка кеша при обновлении MainSection.
     *
     * @param  MainSection  $mainSection
     * @return void
     */
    public function updated(MainSection $mainSection)
    {
        Cache::forget('navigation');
    }

    /**
     * Очистка кеша при удалении MainSection.
     *
     * @param  MainSection  $mainSection
     * @return void
     */
    public function deleted(MainSection $mainSection)
    {
        Cache::forget('navigation');
    }
}
