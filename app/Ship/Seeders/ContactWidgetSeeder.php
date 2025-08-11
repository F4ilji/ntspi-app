<?php

namespace App\Ship\Seeders;

use App\Containers\Widget\Models\ContactWidget;
use App\Ship\Abstracts\Seeders\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ContactWidgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $data = [
            'id' => 1,
            'title' => 'Главная страница контакты',
            'slug' => 'glavnaia-stranica-kontakty',
            'content' => [
                [
                    "title" => "Контакты",
                    "items" => [
                        [
                            "header" => "Главный корпус",
                            "details" => [
                                [
                                    "content" => "622031, Свердловская обл. Нижний Тагил ул. Красногвардейская, 57",
                                    "url" => null
                                ]
                            ]
                        ],
                        [
                            "header" => "Свяжитесь с нами",
                            "details" => [
                                [
                                    "content" => "(3435) 25-48-00",
                                    "url" => null
                                ],
                                [
                                    "content" => "office@ntspi.ru",
                                    "url" => null
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "title" => "Приемная комиссия",
                    "items" => [
                        [
                            "header" => "Расписание",
                            "details" => [
                                [
                                    "content" => "Понедельник - Пятница с 08.30 до 17.00",
                                    "url" => null
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "title" => "Полезное",
                    "items" => [
                        [
                            "header" => "Главный корпус",
                            "details" => [
                                [
                                    "content" => "Виртуальный тур",
                                    "url" => "https://ntspi.ru/panorama/tour.html"
                                ]
                            ]
                        ],
                        [
                            "header" => "Корпус ФХО",
                            "details" => [
                                [
                                    "content" => "Проспект Мира, 25",
                                    "url" => null
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'is_active' => 1,
            'created_at' => '2025-01-09 12:50:17',
            'updated_at' => '2025-05-20 09:18:46',
        ];

        ContactWidget::create($data);
    }
}