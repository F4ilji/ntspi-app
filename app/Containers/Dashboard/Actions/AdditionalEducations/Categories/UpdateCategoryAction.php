<?php

namespace App\Containers\Dashboard\Actions\AdditionalEducations\Categories;

use App\Containers\AdditionalEducation\Models\AdditionalEducationCategory;

class UpdateCategoryAction
{
    public function run(AdditionalEducationCategory $category, array $data): AdditionalEducationCategory
    {
        $category->update($data);
        return $category;
    }
}
