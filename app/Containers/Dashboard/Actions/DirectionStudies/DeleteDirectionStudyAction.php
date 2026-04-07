<?php

namespace App\Containers\Dashboard\Actions\DirectionStudies;

use App\Containers\Education\Models\DirectionStudy;

class DeleteDirectionStudyAction
{
    public function run(DirectionStudy $direction): bool
    {
        return $direction->delete();
    }
}
