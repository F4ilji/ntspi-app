<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EducationalGroupFactory extends Factory
{
    protected $model = \App\Containers\Schedule\Models\EducationalGroup::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
            'faculty_id' => \App\Containers\InstituteStructure\Models\Faculty::inRandomOrder()->first(),
            'education_form_id' => $this->faker->numberBetween(1, 3),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}


