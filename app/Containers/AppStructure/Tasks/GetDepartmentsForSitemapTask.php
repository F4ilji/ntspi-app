<?php

namespace App\Containers\AppStructure\Tasks;

use App\Containers\InstituteStructure\Models\Department;

class GetDepartmentsForSitemapTask
{
    public function run()
    {
        return Department::query()
            ->where('is_active', true)
            ->with('faculty')
            ->get();
    }
}
