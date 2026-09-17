<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DivisionFactory extends Factory
{
    protected $model = \App\Containers\InstituteStructure\Models\Division::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->words(3, true);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => ['description' => $this->faker->paragraph],
            'is_active' => $this->faker->boolean(80),
            'search_data' => $this->faker->words(5, true),
        ];
    }
}
