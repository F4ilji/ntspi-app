<?php

namespace App\Filament\Resources\AdmissionCampaignResource\Pages;

use App\Filament\Resources\AdmissionCampaignResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAdmissionCampaign extends EditRecord
{
    protected static string $resource = AdmissionCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('fetchData') // Кастомная кнопка
            ->label('Обновить данные о местах набора')
                ->color('primary') // Цвет кнопки
                ->icon('heroicon-o-arrow-path') // Иконка
                ->action(function () {
                    // Отправляем запрос в фоновом режиме
                    $this->js(<<<JS
                        fetch('/api/get-admission-plans-data', {
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
            Actions\DeleteAction::make(),
        ];
    }
}
