<?php

namespace App\Containers\Widget\Tasks;

use App\Containers\AdditionalEducation\Models\AdditionalEducation;
use App\Containers\Search\UI\API\Transformers\AdditionalEducationSearchResource;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class GetActiveAdditionalEducationalProgramsTask
{
    public function run()
    {
        return Cache::remember(
            CacheKeys::ADDITIONAL_EDUCATIONAL_PROGRAMS_PREFIX->value . 'search_list',
            now()->addDay(), // Кешируем на 1 день
            function () {
                return AdditionalEducationSearchResource::collection(
                    AdditionalEducation::query()
                        ->where('is_active', true)
                        ->orderBy('title', 'desc')
                        ->get()
                );
            }
        );
    }
}
