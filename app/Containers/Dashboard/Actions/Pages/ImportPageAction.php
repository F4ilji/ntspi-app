<?php

namespace App\Containers\Dashboard\Actions\Pages;

use App\Containers\AppStructure\Models\MainSection;
use App\Containers\AppStructure\Models\Page;
use App\Containers\AppStructure\Models\SubSection;
use App\Containers\Dashboard\Tasks\Content\GenerateSearchDataTask;

class ImportPageAction
{
    public function __construct(
        private readonly GenerateSearchDataTask $generateSearchDataTask,
    ) {}

    /**
     * Импортирует страницу из JSON-структуры
     *
     * @param array $data Раскодированный JSON-массив экспорта
     * @return Page Созданная страница
     * @throws \InvalidArgumentException
     */
    public function run(array $data): Page
    {
        if (empty($data['page']['title'])) {
            throw new \InvalidArgumentException('Отсутствует обязательное поле "page.title"');
        }

        if (empty($data['page']['slug'])) {
            throw new \InvalidArgumentException('Отсутствует обязательное поле "page.slug"');
        }

        $pageData = $data['page'];

        // Resolve sub_section_id via slug path
        $pageData['sub_section_id'] = $this->resolveSubSectionId($data['section_path'] ?? null);

        // Generate search_data from content
        if (!empty($pageData['content'])) {
            $pageData['search_data'] = $this->generateSearchDataTask->run($pageData['content']);
        }

        // Remove fields that should not be set during import
        unset($pageData['icon']);

        $page = Page::create($pageData);

        // Attach SEO if present
        if (!empty($data['seo'])) {
            $page->seo()->create([
                'title' => $data['seo']['title'] ?? null,
                'description' => $data['seo']['description'] ?? null,
            ]);
        }

        return $page;
    }

    private function resolveSubSectionId(?array $sectionPath): ?int
    {
        if (empty($sectionPath)) {
            return null;
        }

        $mainSectionSlug = $sectionPath['main_section_slug'] ?? null;
        $subSectionSlug = $sectionPath['sub_section_slug'] ?? null;

        if (!$mainSectionSlug || !$subSectionSlug) {
            return null;
        }

        $mainSection = MainSection::where('slug', $mainSectionSlug)->first();

        if (!$mainSection) {
            return null;
        }

        $subSection = SubSection::where('main_section_id', $mainSection->id)
            ->where('slug', $subSectionSlug)
            ->first();

        return $subSection?->id;
    }
}
