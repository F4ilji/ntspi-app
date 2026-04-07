<?php

namespace App\Containers\Dashboard\Actions\AdditionalEducations\Directions;

use App\Containers\AdditionalEducation\Models\DirectionAdditionalEducation;

class CreateDirectionAction
{
    public function run(array $data): DirectionAdditionalEducation
    {
        return DirectionAdditionalEducation::create($data);
    }
}
