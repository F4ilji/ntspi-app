<?php

namespace Database\Factories;

use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence;
        $slug = Str::slug($title);
        $content = array_map(function () {
            return [
                'type' => $this->faker->randomElement(['heading', 'paragraph']),
                'data' => [
                    'id' => $this->faker->numberBetween(1, 1000000),
                    'content' => $this->faker->paragraph,
                ],
            ];
        }, range(1, $this->faker->numberBetween(1, 5)));
        $faculty_id = Faculty::inRandomOrder()->first();
        return [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'faculty_id' => $faculty_id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
