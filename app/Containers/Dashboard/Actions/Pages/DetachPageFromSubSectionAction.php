<?php

namespace App\Containers\Dashboard\Actions\Pages;

use App\Containers\AppStructure\Models\Page;

class DetachPageFromSubSectionAction
{
    public function run(Page $page): Page
    {
        $page->section()->dissociate();
        $page->save();

        return $page;
    }
}
