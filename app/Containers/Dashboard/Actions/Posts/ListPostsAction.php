<?php

namespace App\Containers\Dashboard\Actions\Posts;

use App\Containers\Dashboard\Tasks\Posts\ListPostsTask;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListPostsAction
{
    public function __construct(
        private readonly ListPostsTask $listPostsTask,
    ) {}

    public function run(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->listPostsTask->run($filters, $perPage);
    }
}
