<?php

namespace App\Containers\Dashboard\Actions\SubSections;

use App\Containers\AppStructure\Models\SubSection;

class AttachSubSectionToMainSectionAction
{
    public function run(SubSection $subSection, int $mainSectionId): SubSection
    {
        $subSection->update(['main_section_id' => $mainSectionId]);

        return $subSection->fresh();
    }
}
