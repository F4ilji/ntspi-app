<?php

namespace App\Containers\Article\Tasks;

use App\Containers\Article\Models\Category;
use App\Containers\Article\UI\WEB\Transformers\CategoryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class GetCategoriesTask
{
    public function run(): AnonymousResourceCollection
    {
        return Cache::remember('categories', now()->addHours(48), function () {
            return CategoryResource::collection(Category::has('posts')->get());
        });
    }
}