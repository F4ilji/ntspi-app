<?php

namespace App\Containers\Dashboard\Actions\EducationalPrograms;

use App\Containers\Education\Models\EducationalProgram;

class CreateEducationalProgramAction
{
    public function run(array $data): EducationalProgram
    {
        return EducationalProgram::create($data);
    }
}
