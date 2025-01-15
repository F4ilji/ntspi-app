<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Http\Resources\ClientPostListResource;
use App\Http\Resources\PostThumbnailResource;
use App\Models\Post;
use Illuminate\Http\Request;
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
                Post::query()->with('category')->find($id)
            );
        });
    }
}
