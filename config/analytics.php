<?php

return [

    'group_labels' => [
        'structure' => 'Структура сайта',
        'institute' => 'Институт',
        'content' => 'Контент',
        'services' => 'Сервисы',
    ],

    'sections' => [
        '/' => [
            'label' => 'Главная',
            'icon' => 'home',
            'order' => 0,
        ],
        '/faculties' => [
            'label' => 'Факультеты',
            'icon' => 'academic-cap',
            'order' => 1,
            'group' => 'institute',
            'children' => [
                'model' => \App\Containers\InstituteStructure\Models\Faculty::class,
                'name_key' => 'title',
                'slug_key' => 'slug',
            ],
        ],
        '/divisions' => [
            'label' => 'Подразделения',
            'icon' => 'building',
            'order' => 2,
            'group' => 'institute',
            'children' => [
                'model' => \App\Containers\InstituteStructure\Models\Division::class,
                'name_key' => 'title',
                'slug_key' => 'slug',
            ],
        ],
        '/news' => [
            'label' => 'Новости',
            'icon' => 'document',
            'order' => 3,
            'group' => 'content',
        ],
        '/program' => [
            'label' => 'Программы',
            'icon' => 'book-open',
            'order' => 4,
            'group' => 'content',
        ],
        '/schedule' => [
            'label' => 'Расписание',
            'icon' => 'clock',
            'order' => 6,
            'group' => 'services',
        ],
        '/additional-education' => [
            'label' => 'ДПО',
            'icon' => 'academic-cap',
            'order' => 7,
            'group' => 'content',
            'children' => [
                'model' => \App\Containers\AdditionalEducation\Models\AdditionalEducation::class,
                'name_key' => 'title',
                'slug_key' => 'slug',
            ],
        ],
        '/academic-journals' => [
            'label' => 'Журналы',
            'icon' => 'beaker',
            'order' => 8,
            'group' => 'content',
            'children' => [
                'model' => \App\Containers\Science\Models\AcademicJournal::class,
                'name_key' => 'title',
                'slug_key' => 'slug',
            ],
        ],
        '/persons' => [
            'label' => 'Сотрудники',
            'icon' => 'user',
            'order' => 9,
            'group' => 'institute',
        ],
    ],

];
