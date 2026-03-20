<?php

namespace Database\Factories;

use App\Containers\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDetail>
 */
class UserDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Получаем пользователей, у которых еще нет UserDetail

        // Генерация случайного количества наград и других полей
        $awardsCount = rand(1, 5);
        $awards = [];
        for ($i = 0; $i < $awardsCount; $i++) {
            $awards[] = ['item' => $this->faker->sentence(6)];
        }

        $generateItems = function () {
            $itemsCount = rand(1, 5); // Случайное число от 1 до 5
            $items = [];
            for ($i = 0; $i < $itemsCount; $i++) {
                $items[] = ['item' => $this->faker->sentence(6)];
            }
            return $items;
        };

        return [
            'user_id' => null, // Это будет установлено позже
            'is_only_worker' => $this->faker->boolean,
            'photo' => "images/01J7TKMCR55KY7ARS2DA6VVAHE.jpg",
            'academicTitle' => $this->faker->word,
            'AcademicDegree' => $this->faker->word,
            'education' => $this->faker->sentence,
            'awards' => $awards,
            'professDisciplines' => $generateItems(),
            'professionalRetraining' => $generateItems(),
            'professionalDevelopment' => $generateItems(),
            'workExperience' => $this->faker->randomDigit(),
            'attendedConferences' => $generateItems(),
            'participationScienceProjects' => $generateItems(),
            'publications' => $generateItems(),
            'contactEmail' => $this->faker->unique()->safeEmail,
            'contactPhone' => $this->faker->phoneNumber,
        ];
    }
}