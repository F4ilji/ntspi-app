<?php

namespace Database\Factories;

use App\Containers\AppStructure\Models\SubSection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageFactory extends Factory
{
    protected $model = \App\Containers\AppStructure\Models\Page::class;

    public function definition(): array
    {
        $title = $this->faker->words(3, true);
        $slug = Str::slug($title);

        return [
            'title' => $title,
            'slug' => $slug,
            'path' => $slug,
            'content' => [['type' => 'paragraph', 'data' => ['content' => $this->faker->paragraph]]],
            'is_registered' => false,
            'is_visible' => true,
            'searchable' => true,
            'is_url' => false,
            'code' => 200,
            'sub_section_id' => null,
            'search_data' => $this->faker->words(5, true),
            'settings' => null,
            'sort' => $this->faker->numberBetween(1, 100),
            'icon' => null,
        ];
    }
}
