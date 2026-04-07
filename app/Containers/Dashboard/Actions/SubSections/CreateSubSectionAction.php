<?php

namespace App\Containers\Dashboard\Actions\SubSections;

use App\Containers\AppStructure\Models\SubSection;

class CreateSubSectionAction
{
    public function run(array $data): SubSection
    {
        return SubSection::create([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'main_section_id' => $data['main_section_id'] ?? null,
        ]);
    }
}
