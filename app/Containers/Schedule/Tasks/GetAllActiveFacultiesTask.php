<?php

namespace App\Containers\Schedule\Tasks;

use App\Containers\InstituteStructure\Models\Faculty;

class GetAllActiveFacultiesTask
{
    public function run()
    {
        return Faculty::query()->where('is_active', true)->get();
    }
}
