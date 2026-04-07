<?php

namespace App\Containers\Dashboard\Tasks\Posts;

use App\Containers\Article\Models\Post;

class BulkDeletePostsTask
{
    public function run(array $ids): int
    {
        return Post::whereIn('id', $ids)->delete();
    }
}
