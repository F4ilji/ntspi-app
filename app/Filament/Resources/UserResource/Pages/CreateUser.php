<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Containers\User\Models\User;
use App\Services\Filament\Traits\SeoGenerate;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    use SeoGenerate;

    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = $this->generateUniqueSlug($data['name']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->createSeo($this->record);
    }

    private function generateUniqueSlug(string $name): string
    {
        // Преобразуем имя в slug (например, заменяем пробелы на дефисы и приводим к нижнему регистру)
        $slug = Str::slug($name);

        // Проверяем, существует ли slug в базе данных
        $count = 1;
        $baseSlug = $slug;

        while (User::where('slug', $slug)->exists()) {
            // Если slug существует, добавляем суффикс
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

}
