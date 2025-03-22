<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Department;
use App\Models\EducationalProgram;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Faculty;
use App\Models\Post;
use App\Models\User;
use App\Models\UserDetail;
use Faker\Factory;
use Database\Factories\WorkerDepartmentFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{


    public function run(): void
    {
        $faker = Factory::create();
//        User::create([
//            'name' => 'Failj',
//            'email' => 'Failj@bk.ru',
//            'slug' => 'failj',
//            'password' => "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi",
//        ]);
        User::factory()->count(50)->has(UserDetail::factory())->create();
        EventCategory::factory()->count(10)->create();
        Category::factory()->count(80)->create();
        Post::factory()->count(1000)->create();
        Event::factory()->count(200)->create();
        Faculty::factory()->count(6)->create();
        Department::factory()->count(12)->create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('workers_departments')->insert([
                'user_id' => User::inRandomOrder()->first()->id,
                'department_id' => Department::inRandomOrder()->first()->id,
                'position' =>  $faker->word,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($i = 0; $i < 20; $i++) {
            DB::table('workers_faculties')->insert([
                'user_id' => User::inRandomOrder()->first()->id,
                'faculty_id' => Faculty::inRandomOrder()->first()->id,
                'position' =>  $faker->word,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($i = 0; $i < 30; $i++) {
            DB::table('teachers_departments')->insert([
                'user_id' => User::inRandomOrder()->first()->id,
                'department_id' => Department::inRandomOrder()->first()->id,
                'teaching_position' =>  $faker->word,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->call([RolesSeeder::class]);
        $users = [
            [
                'name' => 'Admin',
                'email' => 'Admin@mail.ru',
                'password' => 'R177p900',
                'role' => 'admin',
            ],
            [
                'name' => 'John',
                'email' => 'Test@mail.ru',
                'password' => 'R177p900',
                'role' => 'user',
            ]
        ];
        foreach ($users as $user) {
            $created_user = User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make($user['password']),
            ]);
            $created_user->assignRole($user['role']);
        }
    }
}
