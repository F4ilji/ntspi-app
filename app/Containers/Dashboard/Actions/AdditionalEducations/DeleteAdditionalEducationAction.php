<?php

namespace App\Containers\Dashboard\Actions\AdditionalEducations;

use App\Containers\AdditionalEducation\Models\AdditionalEducation;

class DeleteAdditionalEducationAction
{
    public function run(AdditionalEducation $education): bool
    {
        return $education->delete();
    }
}
