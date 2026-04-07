<?php

namespace App\Containers\Dashboard\Tasks\Posts;

use App\Containers\Article\Models\Post;

class DeletePostTask
{
    public function run(Post $post): bool
    {
        return $post->delete();
    }
}
