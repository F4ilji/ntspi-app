<?php

namespace App\Containers\Dashboard\Actions\Categories;

use App\Containers\Article\Models\Category;
use Illuminate\Support\Str;

class CreateCategoryAction
{
    public function run(array $data): Category
    {
        $data['slug'] = Str::slug($data['title']);

        return Category::create($data);
    }
}
