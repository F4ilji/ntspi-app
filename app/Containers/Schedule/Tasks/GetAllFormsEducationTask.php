<?php

namespace App\Containers\Schedule\Tasks;

use App\Ship\Enums\Education\FormEducation;

class GetAllFormsEducationTask
{
    public function run(): array
    {
        $forms_education = [];
        foreach (FormEducation::cases() as $case) {
            $forms_education[$case->name] = $case->getLabel();
        }

        return $forms_education;
    }
}
