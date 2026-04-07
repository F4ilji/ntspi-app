<?php

namespace App\Containers\Dashboard\Actions\SubSections;

use App\Containers\AppStructure\Models\SubSection;

class DetachSubSectionFromMainSectionAction
{
    public function run(SubSection $subSection): SubSection
    {
        $subSection->update(['main_section_id' => null]);

        return $subSection->fresh();
    }
}
