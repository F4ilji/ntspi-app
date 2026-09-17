<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AcademicJournalFactory extends Factory
{
    protected $model = \App\Containers\Science\Models\AcademicJournal::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->words(3, true);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'main_info' => ['description' => $this->faker->paragraph],
            'chief_editor' => ['name' => $this->faker->name],
            'editors' => [['name' => $this->faker->name], ['name' => $this->faker->name]],
            'for_authors' => ['requirements' => $this->faker->paragraph],
            'is_active' => $this->faker->boolean(80),
            'search_data' => $this->faker->words(5, true),
        ];
    }
}
