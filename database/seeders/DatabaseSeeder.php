<?php

namespace Database\Seeders;

use App\Containers\InstituteStructure\Models\Faculty;
use App\Containers\Article\Models\Post;
use App\Containers\Schedule\Models\EducationalGroup;
use App\Containers\Schedule\Models\Schedule;
use App\Containers\Widget\Data\Seeders\SlideSeeder;
use App\Ship\Seeders\ContactWidgetSeeder;
use App\Ship\Seeders\PageReferenceListSeeder;
use App\Ship\Seeders\SliderSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{


    public function run(): void
    {

        $this->call([
            ContactWidgetSeeder::class,
            PageReferenceListSeeder::class,
            SliderSeeder::class,
            SlideSeeder::class,
        ]);



//        $faker = Factory::create();
//        User::create([
//            'name' => 'Failj',
//            'email' => 'Failj@bk.ru',
//            'slug' => 'failj',
//            'password' => "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi",
//        ]);
//        User::factory()->count(50)->has(UserDetail::factory())->create();
//        EventCategory::factory()->count(10)->create();
//        Category::factory()->count(80)->create();
//        Post::factory()->count(1000)->create();
//        Event::factory()->count(200)->create();
//        $faculties = Faculty::factory()->count(6)->create();

// Затем создаем 40 групп, распределяя их по созданным факультетам
//        EducationalGroup::factory()
//            ->count(40)
//            ->sequence(fn () => [
//                'faculty_id' => $faculties->random()->id
//            ])
//            ->has(Schedule::factory()->count(90 / 40)) // Распределяем 90 расписаний по 40 группам
//            ->create();
//        Department::factory()->count(12)->create();
//
//        for ($i = 0; $i < 20; $i++) {
//            DB::table('workers_departments')->insert([
//                'user_id' => User::inRandomOrder()->first()->id,
//                'department_id' => Department::inRandomOrder()->first()->id,
//                'position' =>  $faker->word,
//                'created_at' => now(),
//                'updated_at' => now(),
//            ]);
//        }
//
//        for ($i = 0; $i < 20; $i++) {
//            DB::table('workers_faculties')->insert([
//                'user_id' => User::inRandomOrder()->first()->id,
//                'faculty_id' => Faculty::inRandomOrder()->first()->id,
//                'position' =>  $faker->word,
//                'created_at' => now(),
//                'updated_at' => now(),
//            ]);
//        }
//
//        for ($i = 0; $i < 30; $i++) {
//            DB::table('teachers_departments')->insert([
//                'user_id' => User::inRandomOrder()->first()->id,
//                'department_id' => Department::inRandomOrder()->first()->id,
//                'teaching_position' =>  $faker->word,
//                'created_at' => now(),
//                'updated_at' => now(),
//            ]);
//        }
//        $this->call([RolesSeeder::class]);
//        $users = [
//            [
//                'name' => 'Admin',
//                'email' => 'Admin@mail.ru',
//                'password' => 'R177p900',
//                'role' => 'admin',
//            ],
//            [
//                'name' => 'John',
//                'email' => 'Test@mail.ru',
//                'password' => 'R177p900',
//                'role' => 'user',
//            ]
//        ];
//        foreach ($users as $user) {
//            $created_user = User::create([
//                'name' => $user['name'],
//                'email' => $user['email'],
//                'password' => Hash::make($user['password']),
//            ]);
//            $created_user->assignRole($user['role']);
//        }
    }
}
