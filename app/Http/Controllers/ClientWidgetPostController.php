<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Http\Resources\ClientPostListResource;
use App\Http\Resources\PostThumbnailResource;
use App\Models\Post;
use Illuminate\Http\Request;

class ClientWidgetPostController extends Controller
{
    public function index()
    {

        return PostThumbnailResource::collection(
            Post::query()
                ->where('status', PostStatus::PUBLISHED)
                ->when(request()->input('category'), function ($query, $category_id) {
                    $query->where('category_id', $category_id);
                })
                ->with('category')
                ->orderBy('publish_at', 'desc')
                ->take(request()->input('count', 5))
                ->get());
    }

    public function single(int $id)
    {
        return new PostThumbnailResource(
            Post::query()->with('category')->find($id)
        );
    }
}
