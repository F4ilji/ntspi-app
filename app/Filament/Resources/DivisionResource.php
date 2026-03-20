<?php

namespace App\Filament\Resources;

use App\Containers\InstituteStructure\Models\Division;
use App\Filament\Components\Forms\ItemForm\Pages\ContentBuilderItem;
use App\Filament\Resources\DivisionResource\Pages;
use App\Filament\Resources\DivisionResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class DivisionResource extends Resource
{
    protected static ?string $model = Division::class;
    protected static ?string $navigationGroup = 'Структура института';
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $modelLabel = 'Подразделение';
    protected static ?string $pluralModelLabel = 'Подразделения института';
    protected static ?int $navigationSort = 100;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основные настройки')
                    ->collapsible()
                    ->schema([
                        Tabs::make('Конструктор подразделения')
                            ->persistTabInQueryString()
                            ->columnSpanFull()
                            ->tabs([
                                Tabs\Tab::make('Основная информация')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Название подразделения')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                                        if ($operation === 'create') {
                                                            $set('slug', Str::slug($state));
                                                        }
                                                    })
                                                    ->placeholder('Введите полное название подразделения')
                                                    ->helperText('Официальное название, которое будет отображаться на сайте'),

                                                TextInput::make('slug')
                                                    ->label('URL-адрес')
                                                    ->unique(ignoreRecord: true)
                                                    ->required()
                                                    ->readOnly()
                                                    ->helperText('Формируется автоматически из названия')
                                                    ->prefix(fn () => route('client.division.index') . '/')
                                                    ->suffixAction(
                                                        Action::make('copy')
                                                            ->icon('heroicon-s-clipboard-document-check')
                                                            ->action(function ($livewire, $state) {
                                                                $livewire->js(
                                                                    'window.navigator.clipboard.writeText("'. route('client.division.index') . '/' . $state.'");
                    $tooltip("'.__('Copied to clipboard').'", { timeout: 1500 });'
                                                                );
                                                            })),



                                            ]),

                                        Toggle::make('is_active')
                                            ->label('Активно на сайте')
                                            ->default(true)
                                            ->inline(false)
                                            ->helperText('Отключите, чтобы временно скрыть подразделение'),
                                    ]),

                                Tabs\Tab::make('Контент')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        ContentBuilderItem::getItem('description')
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->reorderable('order_column')
            ->paginated([10, 25, 50, 100])
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('title')
                    ->label('Название')
                    ->sortable()
                    ->searchable()
                    ->description(fn (Division $record) => Str::limit($record->slug, 30))
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Последнее обновление')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Статус')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('Только активные')
                    ->query(fn (EloquentBuilder $query) => $query->where('is_active', true))
                    ->default(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Редактировать'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить выбранные')
                        ->modalHeading('Подтверждение удаления')
                        ->modalSubmitActionLabel('Да, удалить')
                        ->modalDescription('Вы уверены, что хотите удалить выбранные подразделения? Это действие нельзя отменить.'),

                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Принудительно удалить')
                        ->modalHeading('Подтверждение удаления')
                        ->modalSubmitActionLabel('Да, удалить безвозвратно')
                        ->modalDescription('Внимание! Это действие окончательно удалит записи из базы данных.'),

                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Восстановить выбранные'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить подразделение'),
            ])
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\WorkersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDivisions::route('/'),
            'create' => Pages\CreateDivision::route('/create'),
            'edit' => Pages\EditDivision::route('/{record}/edit'),
        ];
    }
}