<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['slug'] = $this->generateUniqueSlug($data['name']);

        return $data;
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



    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
