<?php

namespace Database\Factories;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Category;
use App\Containers\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array

    {
        $this->faker->addProvider(new \Xvladqt\Faker\LoremFlickrProvider($this->faker));


        $title = $this->faker->sentence;
        $slug = Str::slug($title);
        $content = array_map(function () {
            return [
                'type' => $this->faker->randomElement(['heading', 'paragraph']),
                'data' => [
                    'id' => $this->faker->numberBetween(1, 1000000),
                    'content' => $this->faker->paragraph,
                ],
            ];
        }, range(1, $this->faker->numberBetween(1, 5)));
        $status = $this->faker->randomElement([PostStatus::VERIFICATION, PostStatus::PUBLISHED, PostStatus::REJECTED]);
        $authorsCount = $this->faker->numberBetween(1, 5);
        $authors = array_fill(0, $authorsCount, $this->faker->name);
        $imagesCount = $this->faker->numberBetween(1, 5);
        $images = array_fill(0, $imagesCount, $this->faker->uuid . '.jpg');
        $category_id = Category::inRandomOrder()->first();
        $user_id = User::inRandomOrder()->first();
        $search_data = $this->faker->words(10, true);
        $reading_time = $this->faker->numberBetween(5, 30);


        return [
            'title' => $title,
            'slug' => $slug,
            'preview_text' => $this->faker->paragraph,
            'content' => $content,
            'status' => $status,
            'authors' => $authors,
            'images' => $images,
            'preview' => null,
            'search_data' => $search_data,
            'reading_time' => $reading_time,
            'category_id' => $category_id,
            'user_id' => $user_id,
            'publish_at' => ($status === PostStatus::PUBLISHED) ? now() : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
