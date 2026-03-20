<?php

namespace App\Containers\Main\Tasks;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;
use App\Containers\Widget\UI\API\Transformers\PostThumbnailResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class GetRecentPostsTask
{
    public function run()
    {
        return Cache::remember('posts_recent', now()->addHour(), function () {
            return $this->getRecentPosts();
        });
    }

    private function getRecentPosts()
    {
        return PostThumbnailResource::collection(
            Post::select('title', 'slug', 'authors', 'preview_text', 'category_id', 'preview', 'search_data', 'publish_at', 'created_at')
                ->with('category')
                ->where('publish_at', '<', Carbon::now())
                ->where('status', '=', PostStatus::PUBLISHED)
                ->orderBy('publish_at', 'desc')
                ->limit(3)
                ->get()
        );
    }
}