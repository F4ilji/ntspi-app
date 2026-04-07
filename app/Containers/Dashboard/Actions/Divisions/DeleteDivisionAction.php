<?php

namespace App\Containers\Dashboard\Actions\Divisions;

use App\Containers\InstituteStructure\Models\Division;

class DeleteDivisionAction
{
    public function run(Division $division): void
    {
        $division->delete();
    }
}
