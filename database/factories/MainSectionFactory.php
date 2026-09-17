<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MainSectionFactory extends Factory
{
    protected $model = \App\Containers\AppStructure\Models\MainSection::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->words(2, true);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'sort' => $this->faker->numberBetween(1, 100),
        ];
    }
}
