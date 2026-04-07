<?php

namespace App\Containers\Dashboard\Actions\Sliders;

use App\Containers\Dashboard\Tasks\Sliders\UploadSlideImageTask;
use App\Containers\Widget\Models\Slider;
use App\Containers\Widget\Models\Slide;
use Illuminate\Http\UploadedFile;

class CreateSlideAction
{
    public function __construct(
        private readonly UploadSlideImageTask $uploadImageTask,
    ) {}

    public function run(Slider $slider, array $data): Slide
    {
        $maxSort = $slider->slides()->max('sort') ?? 0;
        
        // Формируем структуру image как в Filament: {url, shading}
        $imageData = [
            'url' => null,
            'shading' => $data['image_shading'] ?? '0.5',
        ];
        
        // Обрабатываем изображение если это файл
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $uploaded = $this->uploadImageTask->run($data['image']);
            $imageData['url'] = $uploaded['url'];
        }
        
        $data['image'] = $imageData;
        unset($data['image_shading']);
        
        // Формируем settings как в Filament: только text_position и link_text
        $data['settings'] = [
            'text_position' => $data['settings']['text_position'] ?? 'left',
            'link_text' => $data['settings']['link_text'] ?? 'Читать',
        ];
        
        // active_button - отдельное поле в базе (не в settings!)
        // Но Filament хранит его в settings, проверим структуру
        $data['settings']['active_button'] = isset($data['active_button']) ? (bool) $data['active_button'] : true;
        
        // Преобразуем is_active в boolean
        if (isset($data['is_active'])) {
            $data['is_active'] = (bool) $data['is_active'];
        }
        
        $slide = new Slide($data);
        $slide->sort = $maxSort + 1;

        $slider->slides()->save($slide);

        return $slide;
    }
}
