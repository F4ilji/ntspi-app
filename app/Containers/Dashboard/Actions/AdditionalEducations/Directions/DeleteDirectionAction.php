<?php

namespace App\Containers\Dashboard\Actions\AdditionalEducations\Directions;

use App\Containers\AdditionalEducation\Models\DirectionAdditionalEducation;

class DeleteDirectionAction
{
    public function run(DirectionAdditionalEducation $direction): bool
    {
        return $direction->delete();
    }
}
