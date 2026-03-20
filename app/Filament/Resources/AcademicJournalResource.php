<?php

namespace App\Filament\Resources;


use App\Containers\Science\Models\AcademicJournal;
use App\Filament\Components\Forms\ItemForm\Pages\ContentBuilderItem;
use App\Filament\Resources\AcademicJournalResource\Pages;
use App\Filament\Resources\AcademicJournalResource\RelationManagers\JournalsRelationManager;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AcademicJournalResource extends Resource
{
    protected static ?string $navigationGroup = 'Наука';
    public static ?string $label = 'Журнал';
    protected static ?string $pluralLabel = 'Научные журналы';
    protected static ?string $model = AcademicJournal::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основные данные')
                    ->description('Основная информация о научном журнале')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Название журнала')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Введите полное название журнала')
                                    ->helperText('Официальное название журнала как в регистрационных документах')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                        $set('slug', Str::slug($state));
                                    }),
                                TextInput::make('slug')
                                    ->label('URL-адрес')
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->readOnly()
                                    ->helperText('Формируется автоматически из названия')
                                    ->prefix(fn () => route('client.academicJournals.index') . '/')
                                    ->suffixAction(
                                        Action::make('copy')
                                            ->icon('heroicon-s-clipboard-document-check')
                                            ->action(function ($livewire, $state) {
                                                $livewire->js(
                                                    'window.navigator.clipboard.writeText("'. route('client.academicJournals.index') . '/' . $state.'");
                    $tooltip("'.__('Copied to clipboard').'", { timeout: 1500 });'
                                                );
                                            })),
                            ]),
                    ]),

                Tabs::make('Настройки журнала')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Основная информация')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                ContentBuilderItem::getItem('main_info')
                                    ->label('Описание журнала')
                                    ->helperText('Добавьте полное описание журнала, его историю и основные направления'),
                            ]),

                        Tabs\Tab::make('Редакционная коллегия')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Section::make('Главный редактор')
                                    ->description('Информация о главном редакторе журнала')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Repeater::make('chief_editor')->label('')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('ФИО')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Иванов Иван Иванович'),
                                                TextInput::make('academicTitle')
                                                    ->label('Учёная степень')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('д.т.н., профессор'),
                                                TextInput::make('position')
                                                    ->label('Должность')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Главный научный сотрудник'),
                                                TextInput::make('institution')
                                                    ->label('Учреждение')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('МГУ имени М.В. Ломоносова'),
                                            ])
                                            ->maxItems(1)
                                            ->reorderable(false)
                                            ->helperText('Укажите данные главного редактора журнала'),
                                    ]),

                                Section::make('Редакционная коллегия')
                                    ->description('Состав редакционной коллегии журнала')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Repeater::make('editors')->label('')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('ФИО')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Петров Петр Петрович'),
                                                TextInput::make('academicTitle')
                                                    ->label('Учёная степень')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('к.ф.-м.н., доцент'),
                                                TextInput::make('position')
                                                    ->label('Должность')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Доцент кафедры'),
                                                TextInput::make('institution')
                                                    ->label('Учреждение')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('СПбГУ'),
                                            ])
                                            ->collapsed()
                                            ->collapsible()
                                            ->addActionLabel('Добавить редактора')
                                            ->reorderable(true)
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                            ->helperText('Добавьте членов редакционной коллегии журнала'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Для авторов')
                            ->icon('heroicon-o-pencil')
                            ->schema([
                                ContentBuilderItem::getItem('for_authors')
                                    ->label('Информация для авторов')
                                    ->helperText('Разместите требования к статьям, правила оформления и сроки подачи'),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Название журнала')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Редактировать'),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Удалить'),
                Tables\Actions\RestoreAction::make()
                    ->iconButton()
                    ->tooltip('Восстановить'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить выбранное'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Восстановить выбранное'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить журнал'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            JournalsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademicJournals::route('/'),
            'create' => Pages\CreateAcademicJournal::route('/create'),
            'edit' => Pages\EditAcademicJournal::route('/{record}/edit'),
        ];
    }

}