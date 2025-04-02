<?php

namespace App\Filament\Pages;

use App\Jobs\RunBackup;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

use ShuvroRoy\FilamentSpatieLaravelBackup\Pages\Backups as BaseBackups;


class Backups extends BaseBackups
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.pages.backup';

    public function getHeading(): string | Htmlable
    {
        return __('filament-spatie-backup::backup.pages.backups.heading');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Настройки приложения';
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-spatie-backup::backup.pages.backups.navigation.label');
    }

    protected function getActions(): array
    {
        return [
            Action::make('Create Backup')
                ->button()
                ->label(__('filament-spatie-backup::backup.pages.backups.actions.create_backup'))
                ->action('create'),
        ];
    }

    public function openOptionModal(): void
    {
        $this->dispatch('open-modal', id: 'backup-option');
    }

    public function create(string $option = ''): void
    {
        RunBackup::dispatch();

        Notification::make()
            ->title('Резервная копия создается в фоновом режиме')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'backup-option');
    }

    public function shouldDisplayStatusListRecords(): bool
    {
        /** @var FilamentSpatieLaravelBackupPlugin $plugin */
        $plugin = filament()->getPlugin('filament-spatie-backup');

        return $plugin->hasStatusListRecordsTable();
    }
}
