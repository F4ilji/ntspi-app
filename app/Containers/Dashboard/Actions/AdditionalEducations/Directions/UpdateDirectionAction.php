<?php

namespace App\Containers\Dashboard\Actions\AdditionalEducations\Directions;

use App\Containers\AdditionalEducation\Models\DirectionAdditionalEducation;

class UpdateDirectionAction
{
    public function run(DirectionAdditionalEducation $direction, array $data): DirectionAdditionalEducation
    {
        $direction->update($data);
        return $direction;
    }
}
