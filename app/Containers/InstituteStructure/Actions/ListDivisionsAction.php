<?php

namespace App\Containers\InstituteStructure\Actions;

use App\Containers\InstituteStructure\Tasks\GetActiveDivisionsTask;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ListDivisionsAction
{
    public function __construct(private readonly GetActiveDivisionsTask $getActiveDivisionsTask)
    {
    }

    public function run(): AnonymousResourceCollection
    {
        return $this->getActiveDivisionsTask->run();
    }
}
