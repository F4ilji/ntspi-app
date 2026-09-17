<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SliderFactory extends Factory
{
    protected $model = \App\Containers\Widget\Models\Slider::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->words(2, true);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
