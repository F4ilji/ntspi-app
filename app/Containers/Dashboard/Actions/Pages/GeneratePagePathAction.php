<?php

namespace App\Containers\Dashboard\Actions\Pages;

use App\Containers\AppStructure\Models\SubSection;

class GeneratePagePathAction
{
    public function run(string $slug, ?int $subSectionId = null): string
    {
        if ($subSectionId === null) {
            return $slug;
        }

        $subSection = SubSection::with('mainSection')->find($subSectionId);

        if ($subSection === null) {
            return $slug;
        }

        if ($subSection->mainSection === null) {
            return $subSection->slug . '/' . $slug;
        }

        return $subSection->mainSection->slug . '/' . $subSection->slug . '/' . $slug;
    }
}
