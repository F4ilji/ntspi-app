<?php

namespace App\Containers\Dashboard\Actions\Divisions;

use App\Containers\InstituteStructure\Models\Division;
use App\Containers\User\Models\User;
use App\Services\App\Cache\DivisionCacheService;

class DetachDivisionWorkerAction
{
    public function __construct(
        private readonly DivisionCacheService $cacheService,
    ) {}

    public function run(Division $division, User $worker): void
    {
        $division->workers()->detach($worker->id);
        $this->cacheService->clearAllCacheByModel();
    }
}
