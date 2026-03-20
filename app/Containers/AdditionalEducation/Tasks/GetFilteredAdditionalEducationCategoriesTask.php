<?php

namespace App\Containers\AdditionalEducation\Tasks;

use App\Containers\AdditionalEducation\Data\Resources\AdditionalEducationCategoryResource;
use App\Containers\AdditionalEducation\Models\AdditionalEducationCategory;
use App\Ship\Enums\CacheKeys;
use App\Ship\Enums\Education\FormEducation;
use App\Ship\Tasks\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GetFilteredAdditionalEducationCategoriesTask
{
    public function run(Request $request)
    {
        $cacheKey = md5(serialize($request->all()));

        return Cache::remember(
            CacheKeys::ADDITIONAL_EDUCATIONAL_PROGRAMS_PREFIX->value . $cacheKey,
            now()->addDay(),
            function () use ($request) {
                $query = AdditionalEducationCategory::query()
                    ->has('additionalEducations')
                    ->withActivePrograms()
                    ->where('is_active', true);

                if ($request->has('form')) {
                    $formValue = FormEducation::fromName($request->form)->value;
                    $query->whereHas('additionalEducations', fn ($q) => $q->where('form_education', $formValue))
                        ->with(['additionalEducations' => fn ($q) => $q->where('form_education', $formValue)]);
                }

                if ($request->has('category')) {
                    $slugs = is_array($request->category) ? $request->category : [$request->category];
                    $query->whereIn('slug', $slugs);
                }

                if ($request->has('direction')) {
                    $query->whereHas('direction', fn($q) => $q->where('slug', $request->direction));
                }

                return AdditionalEducationCategoryResource::collection($query->get());
            }
        );
    }
}
