<?php

namespace App\Containers\Dashboard\Actions\Faculties;

use App\Containers\InstituteStructure\Models\Faculty;
use App\Containers\User\Models\User;
use App\Services\App\Cache\FacultyCacheService;

class AttachFacultyWorkerAction
{
    public function __construct(
        private readonly FacultyCacheService $cacheService,
    ) {}

    public function run(Faculty $faculty, User $user, array $data): void
    {
        $faculty->workers()->attach($user->id, [
            'position' => $data['position'],
            'service_email' => $data['service_email'] ?? null,
            'service_phone' => $data['service_phone'] ?? null,
            'cabinet' => $data['cabinet'] ?? null,
            'sort' => $data['sort'] ?? $faculty->workers()->count(),
        ]);

        $this->cacheService->clearAllCacheByModel();
    }
}
