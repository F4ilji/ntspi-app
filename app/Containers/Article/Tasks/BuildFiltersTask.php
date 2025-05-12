<?php

namespace App\Containers\Article\Tasks;

use App\Containers\Article\Models\Category;
use App\Containers\Article\UI\WEB\Transformers\CategoryResource;
use App\Containers\Article\UI\WEB\Transformers\TagResource;
use App\Ship\Builders\FilterBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BuildFiltersTask
{
    public function __construct(
        private readonly FilterBuilder $filterBuilder
    ) {}

    public function run(array $filters): array
    {
        // Сброс фильтров перед новым запуском
        $this->filterBuilder->reset();

        // 1. Поисковый фильтр
        $this->filterBuilder->add(
            key: 'search_filter',
            type: 'search',
            value: $filters['search'] ?? null,
            param: 'search'
        );


        // 2. Категории

        $categoriesContent = [];
        if (!empty($filters['category'])) {
            $categoriesSlugs = Arr::wrap($filters['category']);
            foreach ($categoriesSlugs as $item) {
                $cacheKey = 'category_content_' . $item;
                $categoriesContent[$item] = Cache::remember($cacheKey, now()->addHours(1), function () use ($item) {
                    return new CategoryResource(Category::where('slug', $item)->first());
                });
            }
        }
        $this->filterBuilder->add(
            key: 'category_filter',
            type: 'category',
            value: $categoriesSlugs ?? null,
            param: 'category',
            content: $categoriesContent
        );


        // 3. Теги

        $tagsContent = [];
        if (!empty($filters['tag'])) {
            $tagsSlugs = Arr::wrap($filters['tag']);
            foreach ($tagsSlugs as $item) {
                $cacheKey = 'tag_content_' . $item;
                $tagsContent[$item] = Cache::remember($cacheKey, now()->addHours(1), function () use ($item) {
                    return new TagResource(DB::table('tags')
                        ->where(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.ru'))"), $item)
                        ->first());
                });
            }
        }
        $this->filterBuilder->add(
            key: 'tag_filter',
            type: 'tag',
            value: $tagsSlugs ?? null,
            param: 'tag',
            content: $tagsContent
        );


        // 4. Сортировка
        $this->filterBuilder->add(
            key: 'sortingBy_filter',
            type: 'sort',
            value: $filters['sort'] ?? null,
            param: 'sort'
        );


        return $this->filterBuilder->get();
    }
}