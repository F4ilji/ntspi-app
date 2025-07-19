<?php

namespace App\Containers\Science\Tasks;

use App\Containers\Science\Models\AcademicJournal;
use App\Ship\Enums\CacheKeys;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class GetAllAcademicJournalsTask
{
    public function run(): Collection|null
    {
        return Cache::remember(
            CacheKeys::ACADEMIC_JOURNALS_PREFIX->value . 'list',
            now()->addWeek(),
            function () {
                return AcademicJournal::query()->get();
            }
        );
    }
}
