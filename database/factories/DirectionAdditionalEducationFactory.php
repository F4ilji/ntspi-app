<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DirectionAdditionalEducationFactory extends Factory
{
    protected $model = \App\Containers\AdditionalEducation\Models\DirectionAdditionalEducation::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->words(3, true);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
