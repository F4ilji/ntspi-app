<?php

namespace App\Containers\Dashboard\Actions\MainSections;

use App\Containers\AppStructure\Models\MainSection;

class CreateMainSectionAction
{
    public function run(array $data): MainSection
    {
        return MainSection::create([
            'title' => $data['title'],
            'slug' => $data['slug'],
        ]);
    }
}
