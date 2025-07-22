<?php

namespace App\Containers\Widget\Tasks;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;
use App\Containers\Widget\UI\API\Transformers\PostThumbnailResource;
use Illuminate\Support\Facades\Cache;

class GetFilteredPostsTask
{
    public function run(?int $category_id, int $count)
    {
        $cacheKey = 'posts_' . $category_id . '_' . $count;

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($category_id, $count) {
            return PostThumbnailResource::collection(
                Post::query()
                    ->where('status', PostStatus::PUBLISHED)
                    ->when($category_id, function ($query, $category_id) {
                        $query->where('category_id', $category_id);
                    })
                    ->with('category')
                    ->orderBy('publish_at', 'desc')
                    ->take($count)
                    ->get()
            );
        });
    }
}
