<?php

namespace App\Containers\Dashboard\Actions\AdditionalEducations;

use App\Containers\AdditionalEducation\Models\AdditionalEducation;
use App\Containers\Dashboard\Tasks\AdditionalEducations\GenerateSearchDataTask;

class UpdateAdditionalEducationAction
{
    public function __construct(
        private readonly GenerateSearchDataTask $generateSearchDataTask,
    ) {}

    public function run(AdditionalEducation $education, array $data): AdditionalEducation
    {
        if (isset($data['content'])) {
            $data['search_data'] = $this->generateSearchDataTask->run($data['content']);
        }

        $education->update($data);

        return $education;
    }
}
