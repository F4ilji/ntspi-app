<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        $fileName = $this->faker->unique()->word . '.pdf';

        return [
            'file' => [
                [
                    'title' => $this->faker->randomElement(['Обычное', 'Основное', 'Дополнительное', 'Экзаменационное']),
                    'path' => 'schedules/' . $this->faker->slug . '-' . time() . '.pdf'
                ]
            ],
            'educational_group_id' => \App\Models\EducationalGroup::factory(),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}