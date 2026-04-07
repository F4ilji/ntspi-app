<?php

namespace App\Containers\Dashboard\Actions\Categories;

use App\Containers\Article\Models\Category;
use Illuminate\Support\Str;

class UpdateCategoryAction
{
    public function run(Category $category, array $data): Category
    {
        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $category->update($data);
        return $category->fresh();
    }
}
