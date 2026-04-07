<?php

namespace App\Containers\Dashboard\Actions\EducationalGroups;

use App\Containers\Schedule\Models\EducationalGroup;

class UpdateEducationalGroupAction
{
    public function run(EducationalGroup $group, array $data): EducationalGroup
    {
        $group->update($data);
        return $group->fresh();
    }
}
