<?php

namespace App\Filament\Resources\AdmissionCampaignResource\Pages;

use App\Filament\Resources\AdmissionCampaignResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAdmissionCampaigns extends ListRecords
{
    protected static string $resource = AdmissionCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(), // Стандартная кнопка "Создать"
            Actions\Action::make('fetchData') // Кастомная кнопка
            ->label('Обновить данные')
                ->color('primary') // Цвет кнопки
                ->icon('heroicon-o-arrow-path') // Иконка
                ->action(function () {
                    // Отправляем запрос в фоновом режиме
                    $this->js(<<<JS
                        fetch('/api/get-edu-program-data', {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                            .then(response => response.json())
                            .then(() => {
                                // Показываем уведомление об успешном завершении
                                window.dispatchEvent(new CustomEvent('filament-notify', {
                                    detail: {
                                        type: 'success',
                                        title: 'Данные обновлены',
                                        body: 'Данные успешно обновлены.'
                                    }
                                }));
                            })
                            .catch(() => {
                                // Показываем уведомление об ошибке
                                window.dispatchEvent(new CustomEvent('filament-notify', {
                                    detail: {
                                        type: 'danger',
                                        title: 'Ошибка',
                                        body: 'Произошла ошибка при обновлении данных.'
                                    }
                                }));
                            });
                    JS);

                    // Показываем уведомление
                    Notification::make()
                        ->title('Данные обновляются')
                        ->body('Данные обновляются в фоновом режиме.')
                        ->success()
                        ->send();
                }),
        ];
    }
}