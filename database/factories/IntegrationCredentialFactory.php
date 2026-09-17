<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class IntegrationCredentialFactory extends Factory
{
    protected $model = \App\Containers\Dashboard\Models\IntegrationCredential::class;

    public function definition(): array
    {
        return [
            'provider' => $this->faker->unique()->randomElement(['vk', 'telegram', 'email', '1c']),
            'payload' => ['token' => $this->faker->uuid, 'secret' => $this->faker->sha256],
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
