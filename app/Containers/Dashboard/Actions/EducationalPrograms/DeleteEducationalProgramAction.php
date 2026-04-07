<?php

namespace App\Containers\Dashboard\Actions\EducationalPrograms;

use App\Containers\Education\Models\EducationalProgram;

class DeleteEducationalProgramAction
{
    public function run(EducationalProgram $program): bool
    {
        return $program->delete();
    }
}
