<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ContactWidgetFactory extends Factory
{
    protected $model = \App\Containers\Widget\Models\ContactWidget::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->words(2, true);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => [
                ['type' => 'phone', 'value' => $this->faker->phoneNumber],
                ['type' => 'email', 'value' => $this->faker->safeEmail],
                ['type' => 'address', 'value' => $this->faker->address],
            ],
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
