<?php

namespace App\Containers\Dashboard\Actions\Departments;

use App\Containers\InstituteStructure\Models\Department;

class DeleteDepartmentAction
{
    public function run(Department $department): void
    {
        $department->delete();
    }
}
