<?php

namespace Database\Seeders;

use App\Containers\AdditionalEducation\Models\AdditionalEducation;
use App\Containers\AdditionalEducation\Models\AdditionalEducationCategory;
use App\Containers\AdditionalEducation\Models\DirectionAdditionalEducation;
use App\Containers\AppStructure\Models\MainSection;
use App\Containers\AppStructure\Models\Page;
use App\Containers\AppStructure\Models\SubSection;
use App\Containers\Article\Models\Category;
use App\Containers\Article\Models\Post;
use App\Containers\Article\Models\Tag;
use App\Containers\Dashboard\Models\IntegrationCredential;
use App\Containers\Education\Models\AdmissionCampaign;
use App\Containers\Education\Models\AdmissionPlan;
use App\Containers\Education\Models\DirectionStudy;
use App\Containers\Education\Models\EducationalProgram;
use App\Containers\InstituteStructure\Models\Department;
use App\Containers\InstituteStructure\Models\Division;
use App\Containers\InstituteStructure\Models\Faculty;
use App\Containers\Science\Models\AcademicJournal;
use App\Containers\Science\Models\JournalIssue;
use App\Containers\Schedule\Models\EducationalGroup;
use App\Containers\Schedule\Models\Schedule;
use App\Containers\User\Models\User;
use App\Containers\User\Models\UserDetail;
use App\Containers\Widget\Models\ContactWidget;
use App\Containers\Widget\Models\CustomForm;
use App\Containers\Widget\Models\CustomFormResponse;
use App\Containers\Widget\Models\PageReferenceList;
use App\Containers\Widget\Models\Slider;
use App\Containers\Widget\Models\Slide;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MockDataSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->command?->info('Creating mock data...');

        // 1. Users & Roles
        $this->createUsers();

        // 2. Institute Structure: 6 факультетов, по 2 кафедры на каждый
        $faculties = Faculty::factory()->count(6)->create();

        $departments = collect();
        foreach ($faculties as $faculty) {
            $departments = $departments->merge(
                Department::factory()->count(2)->create(['faculty_id' => $faculty->id])
            );
        }

        $divisions = Division::factory()->count(20)->create();

        // 3. Pivot: workers_faculties, workers_departments, teachers_departments
        $this->attachWorkers($faculties, $departments);

        // 4. Article
        $categories = Category::factory()->count(20)->create();
        Post::factory()->count(1000)->create();
        Tag::factory()->count(30)->create();

        // 5. Education
        $directionStudies = DirectionStudy::factory()->count(10)->create();
        $programs = EducationalProgram::factory()->count(25)
            ->sequence(fn () => ['direction_study_id' => $directionStudies->random()->id])
            ->create();
        $campaigns = AdmissionCampaign::factory()->count(6)->create();
        AdmissionPlan::factory()->count(30)
            ->sequence(fn () => [
                'admission_campaigns_id' => $campaigns->random()->id,
                'educational_programs_id' => $programs->random()->id,
            ])
            ->create();

        // 6. Pivot: program_department
        $this->attachProgramsToDepartments($programs, $departments);

        // 7. Additional Education
        DirectionAdditionalEducation::factory()->count(5)->create();
        AdditionalEducationCategory::factory()->count(12)->create();
        AdditionalEducation::factory()->count(20)->create();

        // 8. Science
        AcademicJournal::factory()->count(4)->create();
        JournalIssue::factory()->count(15)->create();

        // 9. Schedule
        $groups = EducationalGroup::factory()->count(40)->create();
        Schedule::factory()->count(90)->create();

        // 10. Widgets: 1 главный слайдер + 5 слайдов
        Slider::create([
            'title' => 'Главный слайдер',
            'slug' => 'quos-velit-quisquam',
            'is_active' => true,
        ]);

        $mainSlider = Slider::where('slug', 'quos-velit-quisquam')->first();
        Slide::factory()->count(5)->create([
            'slider_id' => $mainSlider->id,
            'is_active' => true,
            'start_time' => now()->subWeek(),
            'end_time' => now()->addMonth(),
        ]);

        // 11. Contact & Reference widgets
        ContactWidget::create([
            'title' => 'Главная страница контакты',
            'slug' => 'glavnaia-stranica-kontakty',
            'content' => [
                [
                    'title' => 'Контакты',
                    'items' => [
                        [
                            'header' => 'Главный корпус',
                            'details' => [
                                ['content' => '622031, Свердловская обл. Нижний Тагил ул. Красногвардейская, 57', 'url' => null],
                            ],
                        ],
                        [
                            'header' => 'Свяжитесь с нами',
                            'details' => [
                                ['content' => '(3435) 25-48-00', 'url' => null],
                                ['content' => 'office@ntspi.ru', 'url' => null],
                            ],
                        ],
                    ],
                ],
            ],
            'is_active' => true,
        ]);

        PageReferenceList::create([
            'title' => 'Главная страница ресурсы',
            'slug' => 'glavnaia-stranica-resurs',
            'content' => [
                ['title' => 'Приемная комиссия', 'url' => '/admission'],
                ['title' => 'Образование', 'url' => '/education'],
                ['title' => 'Наука', 'url' => '/science'],
            ],
            'is_active' => true,
        ]);

        CustomForm::factory()->count(8)->create();
        CustomFormResponse::factory()->count(40)->create();

        // 12. App Structure
        $mainSections = MainSection::factory()->count(5)->create();
        $subSections = SubSection::factory()
            ->count(15)
            ->sequence(fn () => ['main_section_id' => $mainSections->random()->id])
            ->create();

        $pages = collect();
        for ($i = 0; $i < 40; $i++) {
            $ss = $subSections->random();
            $ms = $ss->mainSection;
            $page = Page::factory()->make(['sub_section_id' => $ss->id]);
            $page->path = $ms->slug . '/' . $ss->slug . '/' . $page->slug;
            $page->save();
            $pages->push($page);
        }

        // 13. Integration Credentials
        IntegrationCredential::factory()->count(3)->create();

        $this->command?->info('Mock data created successfully!');
    }

    private function createUsers(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@ntspi.ru'],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        if (!$admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }

        User::firstOrCreate(
            ['email' => 'editor@ntspi.ru'],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'password' => Hash::make('password'),
            ]
        );

        User::factory()->count(50)->create();
        UserDetail::factory()->count(30)->create();
    }

    private function attachWorkers($faculties, $departments): void
    {
        $users = User::all();

        foreach ($faculties as $faculty) {
            $workers = $users->random(fake()->numberBetween(2, 5));
            foreach ($workers as $worker) {
                DB::table('workers_faculties')->insert([
                    'user_id' => $worker->id,
                    'faculty_id' => $faculty->id,
                    'position' => fake()->randomElement(['Преподаватель', 'Ст. преподаватель', 'Доцент', 'Профессор', 'Зав. кафедрой']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        foreach ($departments as $department) {
            $workers = $users->random(fake()->numberBetween(2, 4));
            foreach ($workers as $worker) {
                DB::table('workers_departments')->insert([
                    'user_id' => $worker->id,
                    'department_id' => $department->id,
                    'position' => fake()->randomElement(['Преподаватель', 'Ст. преподаватель', 'Доцент', 'Лаборант']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $teachers = $users->random(fake()->numberBetween(3, 8));
            foreach ($teachers as $teacher) {
                DB::table('teachers_departments')->insert([
                    'user_id' => $teacher->id,
                    'department_id' => $department->id,
                    'teaching_position' => fake()->randomElement(['Преподаватель', 'Ст. преподаватель', 'Доцент', 'Профессор']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function attachProgramsToDepartments($programs, $departments): void
    {
        foreach ($programs as $program) {
            $dept = $departments->random(fake()->numberBetween(1, 3));
            foreach ($dept as $d) {
                DB::table('program_department')->insert([
                    'educational_program_id' => $program->id,
                    'department_id' => $d->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
