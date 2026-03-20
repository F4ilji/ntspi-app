<?php

namespace App\Observers;

use App\Containers\User\Models\User;
use App\Services\App\Cache\UserCacheService;

class UserObserver
{
    private UserCacheService $userCacheService;

    public function __construct()
    {
        $this->userCacheService = app(UserCacheService::class);
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->userCacheService->clearAllCacheByModel();
        if (\Illuminate\Support\Facades\Schema::hasTable('roles')) {
            $user->assignRole(config('filament-shield.dashboard_user.name', 'dashboard_user'));
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $this->userCacheService->clearCache($user);
        $this->userCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->userCacheService->clearCache($user);
        $this->userCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        $this->userCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        $this->userCacheService->clearCache($user);
        $this->userCacheService->clearAllCacheByModel();
    }
}
