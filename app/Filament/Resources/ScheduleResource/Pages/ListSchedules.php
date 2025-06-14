<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('uploadSchedules') // Дайте уникальное имя для вашего действия
            ->label('Быстрая загрузка расписания') // Текст, который будет отображаться на кнопке
            ->url(ScheduleResource::getUrl('upload')) // Используйте getUrl для получения маршрута
            ->color(Color::Indigo)
            ->icon('heroicon-o-arrow-up-tray'), // Опционально: добавьте иконку
        ];
    }
}