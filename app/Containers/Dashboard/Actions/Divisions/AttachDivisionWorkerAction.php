<?php

namespace App\Containers\Dashboard\Actions\Divisions;

use App\Containers\InstituteStructure\Models\Division;
use App\Containers\User\Models\User;
use App\Services\App\Cache\DivisionCacheService;

class AttachDivisionWorkerAction
{
    public function __construct(
        private readonly DivisionCacheService $cacheService,
    ) {}

    public function run(Division $division, User $user, array $data): void
    {
        $division->workers()->attach($user->id, [
            'administrativePosition' => $data['administrativePosition'],
            'service_email' => $data['service_email'] ?? null,
            'service_phone' => $data['service_phone'] ?? null,
            'cabinet' => $data['cabinet'] ?? null,
            'sort' => $data['sort'] ?? $division->workers()->count(),
        ]);

        $this->cacheService->clearAllCacheByModel();
    }
}
