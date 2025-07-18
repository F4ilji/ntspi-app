<?php

namespace App\Containers\AdditionalEducation\Tasks;

use App\Ship\Enums\Education\FormEducation;

class BuildFormsEducationArrayTask
{
    public function run(): array
    {
        return array_reduce(
            FormEducation::cases(),
            fn ($acc, $case) => $acc + [$case->name => $case->getLabel()],
            []
        );
    }
}
