<?php

namespace Database\Factories;

use App\Ship\Enums\Education\LevelEducational;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DirectionStudyFactory extends Factory
{
    protected $model = \App\Containers\Education\Models\DirectionStudy::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        return [
            'uuid' => $this->faker->uuid,
            'name' => $name,
            'slug' => Str::slug($name),
            'code' => $this->faker->numerify('##.##.##'),
            'lvl_edu' => $this->faker->randomElement([
                LevelEducational::BACHELOR->value,
                LevelEducational::MASTER->value,
                LevelEducational::SPECIALIST->value,
            ]),
            'info' => ['description' => $this->faker->paragraph],
        ];
    }
}
