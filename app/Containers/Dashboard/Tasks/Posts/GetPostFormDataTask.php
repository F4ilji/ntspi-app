<?php

namespace App\Containers\Dashboard\Tasks\Posts;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Category;
use App\Containers\Widget\Models\Slider;
use Illuminate\Support\Collection;

class GetPostFormDataTask
{
    public function run(): array
    {
        return [
            'categories' => Category::all(['id', 'title']),
            'sliders' => Slider::where('is_active', true)->get(['id', 'title']),
            'statuses' => PostStatus::cases(),
        ];
    }
}
