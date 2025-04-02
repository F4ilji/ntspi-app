<?php

namespace App\Filament\Resources;

use App\Enums\EducationalProgramStatus;
use App\Enums\LevelEducational;
use App\Filament\Components\Forms\ItemForm\Pages\ContentBuilderItem;
use App\Filament\Resources\EducationalProgramResource\Pages;
use App\Filament\Resources\EducationalProgramResource\RelationManagers\AdmissionPlansRelationManager;
use App\Models\EducationalProgram;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EducationalProgramResource extends Resource
{
    protected static ?string $model = EducationalProgram::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Образование';
    protected static ?string $pluralLabel = 'Образовательные программы';
    protected static ?string $modelLabel = 'Образовательная программа';
    protected static ?string $navigationParentItem = 'Приемная-компания';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('')
                    ->tabs([
                        Tabs\Tab::make('Основная информация')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Название программы')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Введите полное название программы')
                                    ->columnSpanFull()
                                    ->helperText('Официальное название программы как в лицензии'),

                                Grid::make(2)
                                    ->schema([
                                        Select::make('lvl_edu')
                                            ->label('Уровень образования')
                                            ->options(LevelEducational::class)
                                            ->required()
                                            ->native(false)
                                            ->helperText('Выберите уровень образовательной программы'),

                                        Select::make('status')
                                            ->label('Статус программы')
                                            ->options(EducationalProgramStatus::class)
                                            ->required()
                                            ->native(false)
                                            ->helperText('Определяет видимость программы на сайте'),

                                        TextInput::make('lang_stud')
                                            ->label('Язык обучения')
                                            ->required()
                                            ->placeholder('Например: русский, английский')
                                            ->helperText('Укажите основной язык преподавания')
                                            ->columnSpan(2),
                                    ]),
                            ]),

                        Tabs\Tab::make('Описание программы')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                self::getContentBuilder('about_program', 'О программе')
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Особенности программы')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                self::getContentBuilder('program_features', 'Особенности программы')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }
    protected static function getContentBuilder(string $field, string $label): Builder
    {
        return ContentBuilderItem::getItem($field);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Название')
                    ->sortable()
                    ->searchable()
                    ->description(fn ($record) => $record->lang_stud),

                BadgeColumn::make('lvl_edu')
                    ->label('Уровень')
                    ->formatStateUsing(fn ($state) => LevelEducational::tryFrom($state->value)?->getLabel())
                    ->color(fn ($state) => LevelEducational::tryFrom($state->value)?->getColor())
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn ($state) => EducationalProgramStatus::tryFrom($state)?->getLabel())
                    ->color(fn ($state) => EducationalProgramStatus::tryFrom($state)?->getColor())
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('lvl_edu')
                    ->label('Уровень образования')
                    ->options(LevelEducational::class),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус программы')
                    ->options(EducationalProgramStatus::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Редактировать'),

                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->tooltip('Просмотреть'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить выбранные')
                        ->modalHeading('Удаление программ')
                        ->modalDescription('Вы уверены, что хотите удалить выбранные программы?'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить программу'),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            AdmissionPlansRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEducationalPrograms::route('/'),
            'create' => Pages\CreateEducationalProgram::route('/create'),
            'edit' => Pages\EditEducationalProgram::route('/{record}/edit'),
        ];
    }
}