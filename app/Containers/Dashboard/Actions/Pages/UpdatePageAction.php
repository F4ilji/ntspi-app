<?php

namespace App\Containers\Dashboard\Actions\Pages;

use App\Containers\AppStructure\Models\Page;
use App\Containers\Dashboard\Tasks\Content\GenerateSearchDataTask;

class UpdatePageAction
{
    public function __construct(
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

        $page->update($data);

        return $page->fresh();
    }
}
