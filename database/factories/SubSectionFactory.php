<?php

namespace Database\Factories;

use App\Containers\AppStructure\Models\MainSection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SubSectionFactory extends Factory
{
    protected $model = \App\Containers\AppStructure\Models\SubSection::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->words(2, true);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'sort' => $this->faker->numberBetween(1, 100),
            'main_section_id' => MainSection::factory(),
        ];
    }
}
