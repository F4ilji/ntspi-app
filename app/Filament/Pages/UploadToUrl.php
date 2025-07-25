<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;


class UploadToUrl extends Page
{

    protected static ?string $title = 'Быстрая загрузка файла';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.upload-to-url';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Загрузка файла')
                    ->description('Загрузите один файл. После загрузки вы сможете скопировать его путь.')
                    ->schema([
                        FileUpload::make('data.uploadedFile') // Bind directly to data.uploadedFile
                            ->label('Файл для загрузки')
                            ->required()
                            ->acceptedFileTypes(['application/pdf', 'image/*', 'video/*', 'audio/*', 'text/plain', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->maxSize(20000) // 20MB
                            ->disk('public')
                            ->directory('files')
                            ->visibility('public')
                            ->helperText('Загрузите файл. Максимальный размер 20MB.')
                            ->afterStateUpdated(function ($state) {
                                $this->data['uploadedFile'] = $state; // Assign to data array
                                $this->save();
                            }),
                        TextInput::make('uploadedFileUrl')
                            ->label('Путь к файлу')
                            ->columnSpan('full')
                            ->suffixAction(
                              Action::make('copy')
                                  ->icon('heroicon-s-clipboard-document-check')
                                  ->action(function ($livewire, $state, $record) {
                                      $livewire->js(
                                          'window.navigator.clipboard.writeText("'. url('/') . $state.'");
$tooltip("'.__('Copied to clipboard').'", { timeout: 1500 });'
                                      );
                                  })),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        if (empty($this->data['uploadedFile'])) { // Check data property
            Notification::make()
                ->title('Нет файла для загрузки')
                ->warning()
                ->send();
            return;
        }

        try {
            $originalFileNameWithExtension = $this->data['uploadedFile']->getClientOriginalName();
            $originalFileName = pathinfo($originalFileNameWithExtension, PATHINFO_FILENAME);
            $extension = $this->data['uploadedFile']->getClientOriginalExtension();

            $sluggedFileName = Str::slug($originalFileName);
            $uniqueFileName = $sluggedFileName . '-' . md5(uniqid(rand(), true)) . '.' . $extension;
            $path = $this->data['uploadedFile']->storeAs('files', $uniqueFileName, 'public');

            $this->data['uploadedFileUrl'] = Storage::url($path);
            $this->uploadedFileUrl = $this->data['uploadedFileUrl']; // Assign to data array

            Notification::make()
                ->title('Файл успешно загружен')
                ->body("Файл `{$originalFileNameWithExtension}` загружен. Путь: `{$this->data['uploadedFileUrl']}`")
                ->success()
                ->send();

            $this->data['uploadedFile'] = null; // Clear the file upload field
            $this->form->fill($this->data); // Force form re-render, passing current data


        } catch (\Throwable $e) {
            $originalFileNameWithExtension = $this->data['uploadedFile']->getClientOriginalName() ?? 'Unknown';
            Notification::make()
                ->title('Ошибка при загрузке файла')
                ->body("Не удалось загрузить файл `{$originalFileNameWithExtension}`: " . $e->getMessage())
                ->danger()
                ->send();
            report($e);
        }
    }

    protected function getFormActions(): array
    {
        return [
            // Action::make('save')
            //     ->label('Загрузить файл')
            //     ->submit('save')
            //     ->color('primary')
            //     ->icon('heroicon-o-cloud-arrow-up'),
        ];
    }
}
