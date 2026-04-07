<?php

namespace App\Containers\Dashboard\Actions\EducationalPrograms;

use App\Containers\Education\Models\EducationalProgram;

class UpdateEducationalProgramAction
{
    public function run(EducationalProgram $program, array $data): EducationalProgram
    {
        $program->update($data);
        return $program;
    }
}
