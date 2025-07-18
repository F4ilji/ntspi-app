<?php

namespace App\Containers\AdditionalEducation\Tasks;

use App\Containers\AdditionalEducation\Data\Resources\DirectionAdditionalEducationResource;
use App\Containers\AdditionalEducation\Models\DirectionAdditionalEducation;
use App\Ship\Enums\CacheKeys;
use App\Ship\Tasks\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GetDirectionAdditionalEducationsTask
{
    public function run(Request $request)
    {
        $cacheKey = md5(serialize($request->all()));

        return Cache::remember(
            CacheKeys::ADDITIONAL_EDUCATIONAL_PROGRAMS_PREFIX->value . 'directions_' . $cacheKey,
            now()->addDay(),
            function () {
                return DirectionAdditionalEducationResource::collection(
                    DirectionAdditionalEducation::query()
                        ->where('is_active', true)
                        ->whereHas('additionalEducationCategories', fn ($q) => $q->whereHas('additionalEducations'))
                        ->get()
                );
            }
        );
    }
}
