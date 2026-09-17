<?php

namespace Database\Factories;

use App\Containers\AdditionalEducation\Models\DirectionAdditionalEducation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AdditionalEducationCategoryFactory extends Factory
{
    protected $model = \App\Containers\AdditionalEducation\Models\AdditionalEducationCategory::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->words(2, true);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'is_active' => $this->faker->boolean(80),
            'dir_addit_educat_id' => DirectionAdditionalEducation::factory(),
        ];
    }
}
