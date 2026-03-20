<?php

namespace App\Containers\InstituteStructure\Actions;

use App\Containers\InstituteStructure\Tasks\GetActiveFacultiesTask;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListFacultiesAction
{
    public function __construct(private readonly GetActiveFacultiesTask $getActiveFacultiesTask)
    {
    }

    public function run(): AnonymousResourceCollection
    {
        return $this->getActiveFacultiesTask->run();
    }
}
