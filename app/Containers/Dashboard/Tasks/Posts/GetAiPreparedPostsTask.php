<?php

namespace App\Containers\Dashboard\Tasks\Posts;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class GetAiPreparedPostsTask
{
    public function run(): Collection
    {
        return Post::query()
            ->with(['category', 'author'])
            ->whereIn('status', [PostStatus::VERIFICATION->value, PostStatus::REJECTED->value])
            ->orderBy('created_at', 'desc')
            ->get([
                'id',
                'title',
                'slug',
                'preview_text',
                'content',
                'status',
                'authors',
                'preview',
                'images',
                'reading_time',
                'category_id',
                'created_at',
                'updated_at',
            ]);
    }
}
