<?php

namespace App\Containers\Dashboard\Actions\Faculties;

use App\Containers\InstituteStructure\Models\Faculty;

class DeleteFacultyAction
{
    public function run(Faculty $faculty): void
    {
        $faculty->delete();
    }
}
