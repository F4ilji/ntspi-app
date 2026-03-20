<?php

namespace App\Ship\Seeders;

use App\Containers\Widget\Models\Slider;
use App\Ship\Abstracts\Seeders\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Slider::create([
            'id' => 1,
            'title' => 'quos-velit-quisquam',
            'slug' => 'quos-velit-quisquam',
            'is_active' => 1,
            'created_at' => '2025-04-02 22:08:34',
            'updated_at' => '2025-04-02 22:08:34',
        ]);    }
}
