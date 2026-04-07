<?php

namespace App\Containers\Dashboard\Actions\Pages;

use App\Containers\AppStructure\Models\Page;
use App\Containers\Dashboard\Tasks\Content\GenerateSearchDataTask;

class CreatePageAction
{
    public function __construct(
        private readonly GeneratePagePathAction $generatePagePathAction,
        private readonly GenerateSearchDataTask $generateSearchDataTask,
    ) {}

    /**
     * Создает новую страницу
     *
     * @param array $data Валидированные данные формы (title, slug, sub_section_id, code, searchable, icon, content, settings)
     * @return Page Созданная страница
     */
    public function run(array $data): Page
    {
        $subSectionId = $data['sub_section_id'] ?? null;

        // Генерируем path на основе subSection
        $data['path'] = $this->generatePagePathAction->run($data['slug'], $subSectionId);

        // Генерируем search_data из контента
        if (!empty($data['content'])) {
            $data['search_data'] = $this->generateSearchDataTask->run($data['content']);
        }

        // Удаляем sub_section_id — он не является полем модели Page
        unset($data['sub_section_id']);

        return Page::create($data);
    }
}
