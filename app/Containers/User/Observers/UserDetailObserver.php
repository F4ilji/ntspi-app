<?php

namespace App\Containers\User\Observers;

use App\Containers\User\Models\UserDetail;
use App\Services\App\Cache\UserCacheService;

class UserDetailObserver
{
    private UserCacheService $userCacheService;

    public function __construct()
    {
        $this->userCacheService = app(UserCacheService::class);
    }

    /**
     * Handle the User "created" event.
     */
    public function created(UserDetail $user): void
    {
        $this->userCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(UserDetail $user): void
    {
        $this->userCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(UserDetail $user): void
    {
        $this->userCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(UserDetail $user): void
    {
        $this->userCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(UserDetail $user): void
    {
        $this->userCacheService->clearAllCacheByModel();
    }
}
