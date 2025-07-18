<?php

namespace App\Containers\AdditionalEducation\Tasks;

use App\Containers\AdditionalEducation\Data\Resources\AdditionalEducationCategoryResource;
use App\Containers\AdditionalEducation\Models\AdditionalEducationCategory;
use App\Ship\Enums\Education\FormEducation;
use Illuminate\Http\Request;

class BuildAdditionalEducationFiltersTask
{
    public function run(Request $request): array
    {
        $categoriesContent = [];
        if ($request->category) {
            foreach ((array)$request->category as $item) {
                $categoriesContent[$item] = (new GetFilteredAdditionalEducationCategoriesTask())->run(new Request(['category' => $item]))->first();
            }
        }

        $forms_education = array_reduce(
            FormEducation::cases(),
            fn ($acc, $case) => $acc + [$case->name => $case->getLabel()],
            []
        );

        return [
            'direction_filter' => [
                'type' => 'direction',
                'value' => $request->input('direction'),
                'param' => 'direction'
            ],
            'form_education_filter' => [
                'type' => 'form',
                'value' => $request->input('form'),
                'param' => 'form'
            ],
            'category_filter' => [
                'type' => 'category',
                'value' => $request->input('category'),
                'param' => 'category',
                'content' => $categoriesContent,
            ],
        ];
    }
}
