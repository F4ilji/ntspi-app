<?php

namespace App\Containers\Dashboard\Actions\Pages;

use App\Containers\AppStructure\Models\Page;
use App\Containers\AppStructure\Models\SubSection;

class AttachPageToSubSectionAction
{
    public function run(Page $page, SubSection $subSection): Page
    {
        // Pessimistic lock to prevent race conditions
        $page = Page::lockForUpdate()->findOrFail($page->id);

        if ($page->sub_section_id !== null) {
            throw new \InvalidArgumentException('Страница уже принадлежит другому подразделу');
        }

        $page->section()->associate($subSection);
        $page->save();

        return $page;
    }
}
