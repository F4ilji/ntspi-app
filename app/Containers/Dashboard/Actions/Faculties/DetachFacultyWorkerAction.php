<?php

namespace App\Containers\Dashboard\Actions\Faculties;

use App\Containers\InstituteStructure\Models\Faculty;
use App\Containers\User\Models\User;
use App\Services\App\Cache\FacultyCacheService;

class DetachFacultyWorkerAction
{
    public function __construct(
        private readonly FacultyCacheService $cacheService,
    ) {}

    public function run(Faculty $faculty, User $worker): void
    {
        $faculty->workers()->detach($worker->id);
        $this->cacheService->clearAllCacheByModel();
    }
}
