<?php

namespace App\Containers\Dashboard\Actions\AdditionalEducations\Categories;

use App\Containers\AdditionalEducation\Models\AdditionalEducationCategory;

class DeleteCategoryAction
{
    public function run(AdditionalEducationCategory $category): bool
    {
        return $category->delete();
    }
}
