<?php

namespace App\Containers\Widget\Actions;

use App\Containers\Widget\Tasks\GetFilteredPostsTask;

class GetPostsAction
{
    public function __construct(
        private readonly GetFilteredPostsTask $getFilteredPostsTask
    ) {}

    public function run(?int $category_id, int $count)
    {
        return $this->getFilteredPostsTask->run($category_id, $count);
    }
}
