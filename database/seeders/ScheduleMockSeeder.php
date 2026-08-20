<?php

namespace Database\Seeders;

use App\Containers\Schedule\Models\EducationalGroup;
use App\Containers\Schedule\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleMockSeeder extends Seeder
{
    public function run(): void
    {
        $groups = EducationalGroup::all();

        if ($groups->isEmpty()) {
            $this->command?->error('Нет учебных групп. Сначала создайте faculties + educational_groups.');
            return;
        }

        $titles = [
            'Расписание занятий',
            'Экзаменационная сессия',
            'Зачётная неделя',
            'Расписание консультаций',
            'Курсовое проектирование',
            'Производственная практика',
            'Учебная практика',
            'Расписание лекций',
            'Расписание семинаров',
            'Расписание лабораторных',
        ];

        $semesters = ['осень 2025', 'весна 2026', 'осень 2024', 'весна 2025', 'осень 2023'];

        for ($i = 0; $i < 40; $i++) {
            $group = $groups->random();
            $title = $titles[array_rand($titles)];
            $semester = $semesters[array_rand($semesters)];
            $slug = \Illuminate\Support\Str::slug("{$title}-{$semester}-{$group->id}-{$i}");

            Schedule::create([
                'educational_group_id' => $group->id,
                'file' => [
                    [
                        'title' => "{$title} ({$semester})",
                        'path' => "schedules/{$slug}.pdf",
                    ],
                ],
                'created_at' => now()->subDays(rand(0, 60)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        $this->command?->info('Создано 40 мок-расписаний.');
    }
}
