<?php

namespace App\Containers\Widget\Data\Seeders;

use App\Containers\Widget\Models\Slide;
use App\Containers\Widget\Models\Slider;
use App\Ship\Abstracts\Seeders\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $slider = Slider::first();

        if (!$slider) {
            return;
        }

        // Удаляем старые слайды перед созданием новых
        Slide::where('slider_id', $slider->id)->delete();

        // Создаем 5 случайных слайдов, используя фабрику
        Slide::factory()
            ->count(5)
            ->for($slider) // Устанавливаем связь с родительской моделью Slider
            ->create();    }
}
