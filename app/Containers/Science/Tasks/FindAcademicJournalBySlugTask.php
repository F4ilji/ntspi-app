<?php

namespace App\Containers\Science\Tasks;

use App\Containers\Science\Models\AcademicJournal;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class FindAcademicJournalBySlugTask
{
    public function run(string $slug): AcademicJournal|null
    {
        return Cache::remember(
            CacheKeys::ACADEMIC_JOURNAL_PREFIX->value . $slug,
            now()->addWeek(),
            function () use ($slug) {
                return AcademicJournal::query()
                    ->where('slug', $slug)
                    ->firstOrFail();
            }
        );
    }
}
