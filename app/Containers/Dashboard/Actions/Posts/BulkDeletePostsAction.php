<?php

namespace App\Containers\Dashboard\Actions\Posts;

use App\Containers\Dashboard\Tasks\Posts\BulkDeletePostsTask;

class BulkDeletePostsAction
{
    public function __construct(
        private readonly BulkDeletePostsTask $bulkDeletePostsTask,
    ) {}

    public function run(array $ids): int
    {
        return $this->bulkDeletePostsTask->run($ids);
    }
}
