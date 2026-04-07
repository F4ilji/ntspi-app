<?php

namespace App\Containers\Dashboard\Actions\EducationalGroups;

use App\Containers\Schedule\Models\EducationalGroup;

class DeleteEducationalGroupAction
{
    public function run(EducationalGroup $group): bool
    {
        return $group->delete();
    }
}
