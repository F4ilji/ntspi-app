<?php

namespace App\Containers\Dashboard\Actions\DirectionStudies;

use App\Containers\Education\Models\DirectionStudy;

class UpdateDirectionStudyAction
{
    public function run(DirectionStudy $direction, array $data): DirectionStudy
    {
        $direction->update($data);
        return $direction;
    }
}
