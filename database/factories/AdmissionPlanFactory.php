<?php

namespace Database\Factories;

use App\Containers\Education\Models\AdmissionCampaign;
use App\Containers\Education\Models\EducationalProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdmissionPlanFactory extends Factory
{
    protected $model = \App\Containers\Education\Models\AdmissionPlan::class;

    public function definition(): array
    {
        return [
            'educational_programs_id' => EducationalProgram::factory(),
            'admission_campaigns_id' => AdmissionCampaign::factory(),
            'exams' => $this->faker->words(3, true),
            'contests' => [['name' => $this->faker->word, 'places' => $this->faker->numberBetween(5, 50)]],
        ];
    }
}
