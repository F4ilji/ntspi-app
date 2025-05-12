<?php

namespace App\Filament\Resources;

use App\Containers\Widget\Models\Slider;
use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Resources\SliderResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationGroup = 'Виджеты';
    protected static ?string $navigationLabel = 'Слайдеры';
    protected static ?string $modelLabel = 'Слайдер';
    protected static ?string $pluralModelLabel = 'Слайдеры';
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Настройки слайдера')
                    ->description('Основные параметры отображения слайдера')
                    ->collapsible()
                    ->schema([
                        TextInput::make('title')
                            ->label('Название слайдера')
                            ->placeholder('Например: Главный слайдер')
                            ->helperText('Это название будет использоваться в административной панели')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->label('URL-идентификатор')
                            ->helperText('Автоматически генерируется из названия')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->readOnly()
                            ->maxLength(255),

                        Toggle::make('is_active')
                            ->label('Активность слайдера')
                            ->helperText('Отключите, чтобы временно скрыть слайдер')
                            ->default(true)
                            ->inline(false)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Название')
                    ->sortable()
                    ->searchable()
                    ->description(fn (Slider $record) => $record->slug),

                TextColumn::make('slides_count')
                    ->counts('slides')
                    ->label('Кол-во слайдов')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger'),

                TextColumn::make('is_active')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Активен' : 'Неактивен'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Статус активности')
                    ->options([
                        true => 'Активные',
                        false => 'Неактивные',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->tooltip('Редактировать'),

                Tables\Actions\Action::make('manage_slides')
                    ->icon('heroicon-o-photo')
                    ->tooltip('Управление слайдами')
                    ->url(fn (Slider $record) => SliderResource::getUrl('edit', ['record' => $record]) . '?activeRelationManager=0'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->label('Удалить выбранное'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Создать слайдер'),
            ])
            ->defaultSort('title', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SlidesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}