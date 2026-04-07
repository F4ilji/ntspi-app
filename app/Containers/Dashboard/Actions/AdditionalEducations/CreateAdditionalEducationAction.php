<?php

namespace App\Containers\Dashboard\Actions\AdditionalEducations;

use App\Containers\AdditionalEducation\Models\AdditionalEducation;
use App\Containers\Dashboard\Tasks\AdditionalEducations\GenerateSearchDataTask;

class CreateAdditionalEducationAction
{
    public function __construct(
        private readonly GenerateSearchDataTask $generateSearchDataTask,
    ) {}

    public function run(array $data): AdditionalEducation
    {
        $data['search_data'] = $this->generateSearchDataTask->run($data['content'] ?? []);

        return AdditionalEducation::create($data);
    }
}
