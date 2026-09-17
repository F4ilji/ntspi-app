<?php

namespace Database\Factories;

use App\Ship\Enums\Education\AdmissionCampaignStatus;
use App\Ship\Enums\Education\LevelEducational;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdmissionCampaignFactory extends Factory
{
    protected $model = \App\Containers\Education\Models\AdmissionCampaign::class;

    public function definition(): array
    {
        $levels = [
            LevelEducational::BACHELOR->value,
            LevelEducational::MASTER->value,
            LevelEducational::SPECIALIST->value,
        ];

        $info = array_map(function ($lvl) {
            $och = $this->faker->numberBetween(10, 100);
            $zaoch = $this->faker->numberBetween(5, 50);
            return [
                'edu_name' => $lvl,
                'total_programs' => $this->faker->numberBetween(3, 10),
                'och_count' => $och,
                'zaoch_count' => $zaoch,
                'budget_places' => (int) ($och * 0.6),
                'non_budget_places' => (int) ($och * 0.4) + $zaoch,
            ];
        }, $levels);

        return [
            'name' => 'Прием ' . $this->faker->year . ' года',
            'academic_year' => $this->faker->year . '-' . ($this->faker->year + 1),
            'status' => AdmissionCampaignStatus::ACTIVE->value,
            'info' => $info,
        ];
    }
}
