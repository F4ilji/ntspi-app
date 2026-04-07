<?php

namespace App\Containers\Dashboard\Actions\DirectionStudies;

use App\Containers\Education\Models\DirectionStudy;

class CreateDirectionStudyAction
{
    public function run(array $data): DirectionStudy
    {
        return DirectionStudy::create($data);
    }
}
