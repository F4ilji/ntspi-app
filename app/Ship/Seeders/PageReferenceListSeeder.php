<?php

namespace App\Ship\Seeders;

use App\Containers\Widget\Models\PageReferenceList;
use App\Ship\Abstracts\Seeders\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PageReferenceListSeeder extends Seeder
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
            'title' => 'Главная страница ресурс',
            'slug' => 'glavnaia-stranica-resurs',
            'content' => array(
                ['model_select' => null, 'title' => 'Система Moodle НТГСПИ', 'image' => [], 'icon' => 'heroicon-o-academic-cap', 'link' => 'https://do.ntspi.ru/', 'link_text' => 'Перейти'],
                ['model_select' => null, 'title' => 'ЭИОС', 'image' => [], 'icon' => 'heroicon-o-computer-desktop', 'link' => 'https://eios.rsvpu.ru/', 'link_text' => 'Перейти'],
                ['model_select' => 'Custom', 'title' => 'Группа Вконтакте', 'image' => [], 'icon' => 'heroicon-c-user-group', 'link' => 'https://vk.com/ntspi', 'link_text' => 'Читать', 'content' => null],
                ['model_select' => 'Custom', 'title' => 'Книга почёта', 'image' => [], 'link' => 'http://book.ntspi.ru/', 'link_text' => 'Смотреть', 'content' => null, 'icon' => 'heroicon-o-book-open'],
                ['model_select' => 'Custom', 'title' => 'Поступление в ВУЗ онлайн', 'image' => [], 'link' => 'https://minobrnauki.gov.ru/press-center/news/novosti-ministerstva/50667/', 'link_text' => 'Читать', 'content' => null, 'icon' => 'heroicon-o-globe-alt'],
                ['model_select' => 'Custom', 'title' => 'Интернет олимпиады', 'image' => [], 'link' => 'https://olymp.i-exam.ru/', 'link_text' => 'Читать', 'content' => null, 'icon' => 'heroicon-o-cursor-arrow-rays'],
                ['model_select' => 'Custom', 'title' => 'РЕЕСТР рекомендуемых образовательных цифровых сервисов для использования в программах подготовки педагогических кадров', 'image' => [], 'link' => 'https://new.ntspi.ru/storage/files/reestr.pdf', 'link_text' => 'Смотреть', 'content' => null, 'icon' => 'heroicon-o-table-cells']
            ),
            'is_active' => 1,
            'created_at' => '2025-01-09 09:37:27',
            'updated_at' => '2025-05-23 18:57:38',
        ];

        PageReferenceList::create($data);
    }
}
