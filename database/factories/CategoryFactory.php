<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        // Генерируем случайное количество слов от 1 до 3
        $wordCount = rand(1, 3);
        $title = $this->faker->words($wordCount, true); // true объединяет слова в строку
        $slug = Str::slug($title);

        return [
            'title' => $title,
            'slug' => $slug,
        ];
    }
}
