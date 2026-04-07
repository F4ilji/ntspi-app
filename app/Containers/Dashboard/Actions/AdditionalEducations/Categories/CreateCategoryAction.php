<?php

namespace App\Containers\Dashboard\Actions\AdditionalEducations\Categories;

use App\Containers\AdditionalEducation\Models\AdditionalEducationCategory;

class CreateCategoryAction
{
    public function run(array $data): AdditionalEducationCategory
    {
        return AdditionalEducationCategory::create($data);
    }
}
