<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    protected $model = \App\Containers\Article\Models\Tag::class;

    public function definition(): array
    {
        return [
            'name' => ['ru' => $this->faker->unique()->word, 'en' => $this->faker->unique()->word],
            'slug' => ['ru' => $this->faker->unique()->slug(1), 'en' => $this->faker->unique()->slug(1)],
            'type' => $this->faker->optional()->randomElement(['post', 'event', 'page']),
            'order_column' => $this->faker->optional()->numberBetween(1, 100),
        ];
    }
}
