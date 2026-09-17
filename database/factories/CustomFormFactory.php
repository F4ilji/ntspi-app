<?php

namespace Database\Factories;

use App\Containers\Widget\Enums\CustomFormStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomFormFactory extends Factory
{
    protected $model = \App\Containers\Widget\Models\CustomForm::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->sentence,
            'form_id' => $this->faker->unique()->uuid,
            'columns' => [
                ['name' => 'name', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'type' => 'email', 'required' => true],
                ['name' => 'message', 'type' => 'textarea', 'required' => false],
            ],
            'button' => 'Отправить',
            'send_message' => 'Спасибо за обращение!',
            'mail_settings' => ['to' => $this->faker->safeEmail],
            'settings' => null,
            'status' => $this->faker->randomElement([CustomFormStatus::PUBLISHED->value, CustomFormStatus::HIDDEN->value]),
        ];
    }
}
