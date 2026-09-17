<?php

namespace Database\Factories;

use App\Containers\AdditionalEducation\Models\AdditionalEducationCategory;
use App\Ship\Enums\Education\FormEducation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AdditionalEducationFactory extends Factory
{
    protected $model = \App\Containers\AdditionalEducation\Models\AdditionalEducation::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->words(4, true);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => [['type' => 'paragraph', 'data' => ['content' => $this->faker->paragraph]]],
            'category_id' => AdditionalEducationCategory::factory(),
            'target_group' => $this->faker->randomElement(['Студенты', 'Педагоги', 'Специалисты', 'Все']),
            'qualification' => $this->faker->optional()->word,
            'price' => $this->faker->numberBetween(5000, 150000),
            'learning_time' => $this->faker->randomElement([72, 144, 216, 360, 720]),
            'form_education' => $this->faker->randomElement([FormEducation::FULL_TIME->value, FormEducation::FULL_PART_TIME->value, FormEducation::PART_TIME->value]),
            'is_active' => $this->faker->boolean(80),
            'search_data' => $this->faker->words(5, true),
        ];
    }
}
