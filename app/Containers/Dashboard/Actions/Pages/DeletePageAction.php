<?php

namespace App\Containers\Dashboard\Actions\Pages;

use App\Containers\AppStructure\Models\Page;

class DeletePageAction
{
    /**
     * Удаляет страницу
     *
     * @param Page $page Страница для удаления
     * @return bool Результат удаления
     */
    public function run(Page $page): bool
    {
        return $page->delete();
    }
}
