<?php

namespace App\Containers\Widget\UI\API\Controllers;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;
use App\Containers\Widget\UI\API\Transformers\PostThumbnailResource;
use App\Ship\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class ClientWidgetPostController extends Controller
{
    public function index()
    {
        $category_id = request()->input('category');
        $count = request()->input('count', 5);

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

    public function single(int $id)
    {
        $cacheKey = 'post_' . $id;

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($id) {
            return new PostThumbnailResource(
                Post::query()->with('category')->find($id)->firstOrFail()
            );
        });
    }
}
