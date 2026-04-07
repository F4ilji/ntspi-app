<?php

namespace App\Containers\Dashboard\Actions\Posts;

use App\Containers\Article\Models\Post;
use App\Containers\Dashboard\Tasks\Posts\DeletePostTask;

class DeletePostAction
{
    public function __construct(
        private readonly DeletePostTask $deletePostTask,
    ) {}

    public function run(Post $post): bool
    {
        return $this->deletePostTask->run($post);
    }
}
