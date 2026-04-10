<?php

namespace App\Containers\Dashboard\Actions\Pages;

use App\Containers\AppStructure\Models\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReorderPagesAction
{
    /**
     * Reorders pages within a SubSection based on provided order
     *
     * @param int $subSectionId The SubSection ID
     * @param array $pageIds Ordered array of page IDs
     * @return bool
     */
    public function run(int $subSectionId, array $pageIds): bool
    {
        return DB::transaction(function () use ($subSectionId, $pageIds) {
            foreach ($pageIds as $index => $pageId) {
                Page::where('id', $pageId)
                    ->where('sub_section_id', $subSectionId)
                    ->update(['sort' => $index + 1]);
            }

            // Clear navigation cache since sort order changed
            Cache::forget('navigation');

            // Clear page data cache for all pages in this subsection
            // so that section.pages relation is re-fetched with correct order
            $pages = Page::where('sub_section_id', $subSectionId)->get();
            foreach ($pages as $page) {
                $cacheKey = 'page_data_' . md5($page->path);
                Cache::forget($cacheKey);
            }

            return true;
        });
    }
}
