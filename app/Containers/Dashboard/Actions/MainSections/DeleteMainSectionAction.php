<?php

namespace App\Containers\Dashboard\Actions\MainSections;

use App\Containers\AppStructure\Models\MainSection;

class DeleteMainSectionAction
{
    public function run(MainSection $mainSection): bool
    {
        return $mainSection->delete();
    }
}
