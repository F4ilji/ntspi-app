<?php

namespace App\Containers\AppStructure\Tasks;

use App\Containers\Education\Models\EducationalProgram;
use App\Ship\Enums\Education\EducationalProgramStatus;

class GetEducationalProgramsForSitemapTask
{
    public function run()
    {
        return EducationalProgram::query()
            ->where('status', EducationalProgramStatus::PUBLISHED)
            ->get();
    }
}
