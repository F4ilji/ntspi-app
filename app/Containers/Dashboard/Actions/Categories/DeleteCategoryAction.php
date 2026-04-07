<?php

namespace App\Containers\Dashboard\Actions\Categories;

use App\Containers\Article\Models\Category;

class DeleteCategoryAction
{
    public function run(Category $category): bool
    {
        return $category->delete();
    }
}
