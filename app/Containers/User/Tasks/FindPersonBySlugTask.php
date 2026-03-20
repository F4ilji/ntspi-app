<?php

namespace App\Containers\User\Tasks;

use App\Containers\User\Models\User;
use App\Ship\Enums\CacheKeys;
use App\Ship\Parents\Tasks\Task;
use Illuminate\Support\Facades\Cache;

class FindPersonBySlugTask
{
    public function run(string $slug): User
    {
        return Cache::remember(
            CacheKeys::USER_PREFIX->value . $slug,
            now()->addHours(24),
            fn() => User::with(['userDetail', 'departments_work.faculty', 'departments_teach.faculty', 'divisions', 'faculties'])
                ->where('slug', $slug)
                ->whereHas('userDetail')
                ->firstOrFail()
        );
    }
}
