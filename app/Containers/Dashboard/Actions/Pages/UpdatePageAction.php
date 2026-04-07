<?php

namespace App\Containers\Dashboard\Actions\Pages;

use App\Containers\AppStructure\Models\Page;
use App\Containers\Dashboard\Tasks\Content\GenerateSearchDataTask;

class UpdatePageAction
{
    public function __construct(
        private readonly GeneratePagePathAction $generatePagePathAction,
        private readonly GenerateSearchDataTask $generateSearchDataTask,
    ) {}

    /**
     * Обновляет страницу
     *
     * @param Page $page Страница для обновления
     * @param array $data Валидированные данные формы
     * @return Page Обновленная страница
     */
    public function run(Page $page, array $data): Page
    {
        // Если контент изменился, перегенерируем search_data
        if (isset($data['content']) && json_encode($data['content']) !== json_encode($page->content)) {
            $data['search_data'] = $this->generateSearchDataTask->run($data['content']);
        }

        // Если страница не зарегистрирована, перегенерируем path
        if ($page->is_registered == false) {
            $subSectionId = $data['sub_section_id'] ?? $page->sub_section_id;
            $slug = $data['slug'] ?? $page->slug;

            $data['path'] = $this->generatePagePathAction->run($slug, $subSectionId);
        }

        // Удаляем sub_section_id — он не является полем модели Page
        unset($data['sub_section_id']);

        $page->update($data);

        return $page->fresh();
    }
}
