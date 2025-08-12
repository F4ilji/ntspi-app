<?php

namespace App\Containers\Widget\Data\Factories;

use App\Containers\Widget\Models\Slide;
use App\Ship\Abstracts\Factories\Factory;

/**
 * @extends \App\Ship\Abstracts\Factories\Factory<\App\Ship\Models\Model>
 */
class SlideFactory extends Factory
{

    protected $model = Slide::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'content' => $this->faker->paragraph(2),
            'image' => json_encode([
                'url' => null,
                'shading' => $this->faker->randomFloat(1, 0, 1)
            ]),
            'link' => '/news/' . $this->faker->slug,
            'settings' => json_encode([
                'text_position' => $this->faker->randomElement(['center', 'left', 'right']),
                'link_text' => $this->faker->word,
            ]),
            'color_theme' => $this->faker->safeColorName,
            'is_active' => $this->faker->boolean(80), // 80% шанс, что будет true
            'start_time' => $this->faker->dateTimeBetween('-1 week', '+1 week'),
            'end_time' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'sort' => $this->faker->numberBetween(1, 10),
            // slider_id будет установлен в сидере
            'slidable_id' => null,
            'slidable_type' => null,
        ];
    }
}
