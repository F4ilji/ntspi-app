<?php

namespace App\Containers\AppStructure\Tasks;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;

class GetPostsForSitemapTask
{
    public function run()
    {
        return Post::query()
            ->where('status', PostStatus::PUBLISHED)
            ->get();
    }
}
