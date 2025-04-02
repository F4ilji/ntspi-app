<?php

namespace App\Observers;

use App\Models\AcademicJournal;
use App\Services\App\Cache\AcademicJournalCacheService;

class AcademicJournalObserver
{
    private AcademicJournalCacheService $academicJournalCacheService;

    public function __construct()
    {
        $this->academicJournalCacheService = app(AcademicJournalCacheService::class);
    }

    /**
     * Handle the AcademicJournal "created" event.
     */
    public function created(AcademicJournal $academicJournal): void
    {
        $this->academicJournalCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the AcademicJournal "updated" event.
     */
    public function updated(AcademicJournal $academicJournal): void
    {
        $this->academicJournalCacheService->clearCache($academicJournal);
    }

    /**
     * Handle the AcademicJournal "deleted" event.
     */
    public function deleted(AcademicJournal $academicJournal): void
    {
        $this->academicJournalCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the AcademicJournal "restored" event.
     */
    public function restored(AcademicJournal $academicJournal): void
    {
        //
    }

    /**
     * Handle the AcademicJournal "force deleted" event.
     */
    public function forceDeleted(AcademicJournal $academicJournal): void
    {
        //
    }
}