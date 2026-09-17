<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageReferenceListFactory extends Factory
{
    protected $model = \App\Containers\Widget\Models\PageReferenceList::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->words(3, true);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => [
                ['title' => $this->faker->words(2, true), 'url' => $this->faker->url],
                ['title' => $this->faker->words(2, true), 'url' => $this->faker->url],
            ],
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
