<?php

namespace App\Containers\Widget\Tasks;

use App\Containers\Article\Models\Post;
use App\Containers\Widget\UI\API\Transformers\PostThumbnailResource;
use Illuminate\Support\Facades\Cache;

class FindPostByIdTask
{
    public function run(int $id)
    {
        $cacheKey = 'post_' . $id;

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($id) {
            return new PostThumbnailResource(
                Post::query()->with('category')->find($id)->firstOrFail()
            );
        });
    }
}
