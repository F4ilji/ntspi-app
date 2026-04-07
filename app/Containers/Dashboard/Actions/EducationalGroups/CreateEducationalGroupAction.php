<?php

namespace App\Containers\Dashboard\Actions\EducationalGroups;

use App\Containers\Schedule\Models\EducationalGroup;

class CreateEducationalGroupAction
{
    public function run(array $data): EducationalGroup
    {
        return EducationalGroup::create($data);
    }
}
