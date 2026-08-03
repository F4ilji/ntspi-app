<?php

namespace App\Containers\Dashboard\Actions\Pages;

use App\Containers\AppStructure\Models\Page;

class ExportPageAction
{
    public function run(Page $page): array
    {
        $page->load(['section.mainSection', 'seo']);

        $sectionPath = null;

        if ($page->section) {
            $sectionPath = [
                'main_section_slug' => $page->section->mainSection?->slug,
                'sub_section_slug' => $page->section->slug,
            ];
        }

        return [
            'export_version' => 1,
            'exported_at' => now()->toIso8601String(),
            'page' => [
                'title' => $page->title,
                'slug' => $page->slug,
                'code' => $page->code,
                'content' => $page->content,
                'settings' => $page->settings,
                'searchable' => $page->searchable,
                'sort' => $page->sort,
                'is_visible' => $page->is_visible,
            ],
            'seo' => $page->seo ? [
                'title' => $page->seo->title,
                'description' => $page->seo->description,
            ] : null,
            'section_path' => $sectionPath,
        ];
    }
}
