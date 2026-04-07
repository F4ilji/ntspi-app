<?php

namespace App\Containers\Dashboard\Actions\MainSections;

use App\Containers\AppStructure\Models\MainSection;

class UpdateMainSectionAction
{
    public function run(MainSection $mainSection, array $data): MainSection
    {
        $mainSection->update([
            'title' => $data['title'],
            'slug' => $data['slug'],
        ]);

        return $mainSection->fresh();
    }
}
