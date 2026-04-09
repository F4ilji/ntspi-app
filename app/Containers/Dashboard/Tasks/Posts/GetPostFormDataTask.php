<?php

namespace App\Containers\Dashboard\Tasks\Posts;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Category;

class GetPostFormDataTask
{
    public function run(): array
    {
        return [
            'categories' => Category::all(['id', 'title']),
            'statuses' => PostStatus::cases(),
        ];
    }
}
