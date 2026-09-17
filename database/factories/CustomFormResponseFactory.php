<?php

namespace Database\Factories;

use App\Containers\Widget\Models\CustomForm;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomFormResponseFactory extends Factory
{
    protected $model = \App\Containers\Widget\Models\CustomFormResponse::class;

    public function definition(): array
    {
        return [
            'custom_form_id' => CustomForm::factory(),
            'answers' => [
                'name' => $this->faker->name,
                'email' => $this->faker->safeEmail,
                'message' => $this->faker->paragraph,
            ],
            'checked' => $this->faker->boolean(30),
        ];
    }
}
