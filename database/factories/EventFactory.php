<?php

namespace Database\Factories;

use App\Containers\Event\Models\EventCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    protected $model = \App\Containers\Event\Models\Event::class;

    public function definition(): array
    {
        $title = $this->faker->sentence;
        $content = array_map(function () {
            return [
                'type' => $this->faker->randomElement(['heading', 'paragraph']),
                'data' => [
                    'id' => $this->faker->numberBetween(1, 1000000),
                    'content' => $this->faker->paragraph,
                ],
            ];
        }, range(1, $this->faker->numberBetween(1, 5)));

        $eventDateStart = $this->faker->dateTimeBetween('-6 months', 'now');
        $endDate = date('Y-m-d', strtotime($eventDateStart->format('Y-m-d') . ' +' . rand(1, 30) . ' days'));
        $eventDateEnd = $this->faker->optional(0.5)->dateTimeBetween(
            $eventDateStart->format('Y-m-d'),
            $endDate
        );

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'event_date_start' => $eventDateStart->format('Y-m-d'),
            'event_date_end' => $eventDateEnd?->format('Y-m-d'),
            'event_time_start' => $this->faker->time('H:i'),
            'address' => $this->faker->address,
            'is_online' => $this->faker->boolean,
            'category_id' => EventCategory::factory(),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
