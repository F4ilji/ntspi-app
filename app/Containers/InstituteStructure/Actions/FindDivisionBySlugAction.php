<?php

namespace App\Containers\InstituteStructure\Actions;

use App\Containers\InstituteStructure\Models\Division;
use App\Containers\InstituteStructure\Tasks\GetDivisionBySlugTask;

class FindDivisionBySlugAction
{
    public function __construct(private readonly GetDivisionBySlugTask $getDivisionBySlugTask)
    {
    }

    public function run(string $slug): Division
    {
        return $this->getDivisionBySlugTask->run($slug);
    }
}
