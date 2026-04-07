<?php

namespace App\Containers\Dashboard\Actions\Sliders;

use App\Containers\Dashboard\Tasks\Sliders\UploadSlideImageTask;
use App\Containers\Widget\Models\Slide;
use Illuminate\Http\UploadedFile;

class UpdateSlideAction
{
    public function __construct(
        private readonly UploadSlideImageTask $uploadImageTask,
    ) {}

    public function run(Slide $slide, array $data): Slide
    {
        // Формируем структуру image как в Filament: {url, shading}
        $imageData = $slide->image ?? [];
        if (!is_array($imageData)) {
            $imageData = [];
        }
        
        // Обновляем shading если передан
        if (isset($data['image_shading'])) {
            $imageData['shading'] = $data['image_shading'];
            unset($data['image_shading']);
        }
        
        // Обрабатываем новое изображение если это файл
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $uploaded = $this->uploadImageTask->run($data['image']);
            $imageData['url'] = $uploaded['url'];
        }
        
        // Сохраняем image
        if (!empty($imageData)) {
            $data['image'] = $imageData;
        }
        
        // Формируем settings
        if (isset($data['settings'])) {
            $settings = $slide->settings ?? [];
            if (!is_array($settings)) {
                $settings = [];
            }
            
            // Обновляем переданные поля
            foreach ($data['settings'] as $key => $value) {
                $settings[$key] = $value;
            }
            
            // active_button
            if (isset($data['active_button'])) {
                $settings['active_button'] = (bool) $data['active_button'];
                unset($data['active_button']);
            }
            
            $data['settings'] = $settings;
        }
        
        // Преобразуем is_active в boolean
        if (isset($data['is_active'])) {
            $data['is_active'] = (bool) $data['is_active'];
        }
        
        $slide->update($data);

        return $slide;
    }
}
