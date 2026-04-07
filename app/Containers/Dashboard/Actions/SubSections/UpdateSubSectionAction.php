<?php

namespace App\Containers\Dashboard\Actions\SubSections;

use App\Containers\AppStructure\Models\Page;
use App\Containers\AppStructure\Models\SubSection;

class UpdateSubSectionAction
{
    public function run(SubSection $subSection, array $data): SubSection
    {
        $subSection->update([
            'title' => $data['title'],
            'slug' => $data['slug'],
        ]);

        if (isset($data['page_ids'])) {
            $this->syncPages($subSection, $data['page_ids']);
        }

        return $subSection->fresh();
    }

    private function syncPages(SubSection $subSection, array $pageIds): void
    {
        Page::query()->where('sub_section_id', '=', $subSection->id)->update(['sub_section_id' => null]);
        Page::whereIn('id', $pageIds)->update(['sub_section_id' => $subSection->id]);

        $pages = Page::where('is_url', '=', false)
            ->where('sub_section_id', '=', $subSection->id)
            ->get();

        if ($pages->isEmpty() || !$subSection->mainSection) {
            return;
        }

        $mainSectionSlug = $pages->first()->section->mainSection->slug;

        foreach ($pages as $page) {
            if ($page->is_registered != true) {
                $page->update(['path' => $mainSectionSlug . '/' . $subSection->slug . '/' . $page->slug]);
            }
        }
    }
}
