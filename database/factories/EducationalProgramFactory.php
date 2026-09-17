<?php

namespace Database\Factories;

use App\Containers\Education\Models\DirectionStudy;
use App\Ship\Enums\Education\LevelEducational;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EducationalProgramFactory extends Factory
{
    protected $model = \App\Containers\Education\Models\EducationalProgram::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(4, true);
        return [
            'uuid' => $this->faker->uuid,
            'name' => $name,
            'slug' => Str::slug($name),
            'about_program' => ['description' => $this->faker->paragraph],
            'program_features' => ['features' => $this->faker->paragraph],
            'inner_code' => $this->faker->numerify('###'),
            'lvl_edu' => $this->faker->randomElement([
                LevelEducational::BACHELOR->value,
                LevelEducational::MASTER->value,
                LevelEducational::SPECIALIST->value,
            ]),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'lang_stud' => $this->faker->randomElement(['ru', 'en']),
            'learning_forms' => ['full_time', 'part_time'],
            'direction_study_id' => DirectionStudy::factory(),
            'search_data' => $this->faker->words(5, true),
        ];
    }
}
