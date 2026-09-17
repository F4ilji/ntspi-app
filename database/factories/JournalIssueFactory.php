<?php

namespace Database\Factories;

use App\Containers\Science\Models\AcademicJournal;
use Illuminate\Database\Eloquent\Factories\Factory;

class JournalIssueFactory extends Factory
{
    protected $model = \App\Containers\Science\Models\JournalIssue::class;

    public function definition(): array
    {
        return [
            'title' => 'Выпуск ' . $this->faker->numberBetween(1, 12) . '/' . $this->faker->year,
            'path_file' => 'journals/' . $this->faker->uuid . '.pdf',
            'year_publication' => $this->faker->numberBetween(2020, 2025),
            'is_active' => $this->faker->boolean(80),
            'sort' => $this->faker->optional()->numberBetween(1, 100),
            'academic_journal_id' => AcademicJournal::factory(),
        ];
    }
}
