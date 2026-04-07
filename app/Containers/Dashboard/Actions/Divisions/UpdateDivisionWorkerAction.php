<?php

namespace App\Containers\Dashboard\Actions\Divisions;

use App\Containers\InstituteStructure\Models\Division;
use App\Containers\User\Models\User;
use App\Services\App\Cache\DivisionCacheService;

class UpdateDivisionWorkerAction
{
    public function __construct(
        private readonly DivisionCacheService $cacheService,
    ) {}

    public function run(Division $division, User $worker, array $data): void
    {
        $division->workers()->updateExistingPivot($worker->id, [
            'administrativePosition' => $data['administrativePosition'],
            'service_email' => $data['service_email'] ?? null,
            'service_phone' => $data['service_phone'] ?? null,
            'cabinet' => $data['cabinet'] ?? null,
        ]);

        $this->cacheService->clearAllCacheByModel();
    }
}
