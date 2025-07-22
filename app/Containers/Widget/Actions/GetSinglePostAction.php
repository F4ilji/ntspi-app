<?php

namespace App\Containers\Widget\Actions;

use App\Containers\Widget\Tasks\FindPostByIdTask;

class GetSinglePostAction
{
    public function __construct(
        private readonly FindPostByIdTask $findPostByIdTask
    ) {}

    public function run(int $id)
    {
        return $this->findPostByIdTask->run($id);
    }
}
