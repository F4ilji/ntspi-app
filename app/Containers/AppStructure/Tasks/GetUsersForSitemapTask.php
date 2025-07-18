<?php

namespace App\Containers\AppStructure\Tasks;

use App\Containers\User\Models\User;

class GetUsersForSitemapTask
{
    public function run()
    {
        return User::query()
            ->whereHas('userDetail', function ($q) {
                $q->where('is_only_worker', false);
            })
            ->get();
    }
}
