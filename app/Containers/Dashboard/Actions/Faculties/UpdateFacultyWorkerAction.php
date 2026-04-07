<?php

namespace App\Containers\Dashboard\Actions\Faculties;

use App\Containers\InstituteStructure\Models\Faculty;
use App\Containers\User\Models\User;
use App\Services\App\Cache\FacultyCacheService;

class UpdateFacultyWorkerAction
{
    public function __construct(
        private readonly FacultyCacheService $cacheService,
    ) {}

    public function run(Faculty $faculty, User $worker, array $data): void
    {
        $faculty->workers()->updateExistingPivot($worker->id, [
            'position' => $data['position'],
            'service_email' => $data['service_email'] ?? null,
            'service_phone' => $data['service_phone'] ?? null,
            'cabinet' => $data['cabinet'] ?? null,
        ]);

        $this->cacheService->clearAllCacheByModel();
    }
}
