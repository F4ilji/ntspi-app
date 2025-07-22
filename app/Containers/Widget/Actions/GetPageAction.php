<?php

namespace App\Containers\Widget\Actions;

use App\Containers\Widget\Tasks\FindPageByIdTask;
use App\Containers\Widget\UI\API\Transformers\PageNavigateResource;

class GetPageAction
{
    public function __construct(
        private readonly FindPageByIdTask $findPageByIdTask
    ) {}

    public function run(int $id)
    {
        $page = $this->findPageByIdTask->run($id);

        if (isset($page->section)) {
            $breadcrumbs = [
                'mainSection' => $page->section->mainSection->title,
                'subSection' => $page->section->title,
                'page' => $page->title,
            ];
        } else {
            $breadcrumbs = null;
        }

        return [
            'page' => new PageNavigateResource($page),
            'breadcrumbs' => $breadcrumbs
        ];
    }
}
