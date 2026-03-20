<?php

namespace App\Containers\AdditionalEducation\Tasks;

use App\Containers\AdditionalEducation\Data\Resources\AdditionalEducationCategoryPreviewResource;
use App\Containers\AdditionalEducation\Models\AdditionalEducationCategory;
use App\Ship\Enums\CacheKeys;
use App\Ship\Tasks\Task;
use Illuminate\Support\Facades\Cache;

class GetAllAdditionalEducationCategoriesPreviewTask
{
    public function run()
    {
        return Cache::remember(
            CacheKeys::ADDITIONAL_EDUCATIONAL_PROGRAMS_PREFIX->value . 'categories',
            now()->addWeek(),
            function () {
                return AdditionalEducationCategoryPreviewResource::collection(
                    AdditionalEducationCategory::query()
                        ->where('is_active', true)
                        ->has('additionalEducations')
                        ->get()
                );
            }
        );
    }
}
