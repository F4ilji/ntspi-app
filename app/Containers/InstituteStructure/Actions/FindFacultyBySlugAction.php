<?php

namespace App\Containers\InstituteStructure\Actions;

use App\Containers\InstituteStructure\Models\Faculty;
use App\Containers\InstituteStructure\Tasks\GetFacultyBySlugTask;

class FindFacultyBySlugAction
{
    public function __construct(private readonly GetFacultyBySlugTask $getFacultyBySlugTask)
    {
    }

    public function run(string $slug): Faculty
    {
        return $this->getFacultyBySlugTask->run($slug);
    }
}
