<?php

namespace App\Containers\Dashboard\Actions\SubSections;

use App\Containers\AppStructure\Models\SubSection;

class DeleteSubSectionAction
{
    public function run(SubSection $subSection): bool
    {
        return $subSection->delete();
    }
}
