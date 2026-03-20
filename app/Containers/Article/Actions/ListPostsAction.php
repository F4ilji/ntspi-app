<?php

namespace App\Containers\Article\Actions;

use App\Containers\Article\Tasks\BuildFiltersTask;
use App\Containers\Article\Tasks\GetCategoriesTask;
use App\Containers\Article\Tasks\GetPostsTask;
use App\Containers\Article\Tasks\GetTagsTask;
use App\Containers\Article\UI\WEB\Transformers\PostListResource;
use App\Ship\Contracts\SeoServiceInterface;

class ListPostsAction
{
    public function __construct(
        private readonly GetPostsTask $getPostsTask,
        private readonly GetCategoriesTask $getCategoriesTask,
        private readonly GetTagsTask $getTagsTask,
        private readonly BuildFiltersTask $buildFiltersTask,
        private readonly SeoServiceInterface $seoPageProvider,
    ) {}

    public function run(array $filters): array
    {
        $posts = $this->getPostsTask->run($filters);
        $categories = $this->getCategoriesTask->run();
        $tags = $this->getTagsTask->run();
        $filtersData = $this->buildFiltersTask->run($filters);
        $seo = $this->seoPageProvider->getSeoForCurrentPage();

        return [
            'posts' => inertia()->deepMerge(fn() => PostListResource::collection($posts->items())),
            'posts_pagination' => $posts->toArray(),
            'filters' => $filtersData,
            'categories' => $categories,
            'tags' => $tags,
            'seo' => $seo,
        ];
    }
}