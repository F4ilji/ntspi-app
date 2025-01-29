<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactWidgetResource\Pages;
use App\Filament\Resources\ContactWidgetResource\RelationManagers;
use App\Models\ContactWidget;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ContactWidgetResource extends Resource
{
    protected static ?string $model = ContactWidget::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')->schema([
                    Tabs::make('Tabs')
                        ->tabs([
                            Tabs\Tab::make('Основная информация')
                                ->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        TextInput::make('title')->label('Название ресурса')->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                                $set('slug', Str::slug($state));
                                                $set('seo.title', $state);
                                            }),
                                        TextInput::make('slug')->label('Slug')->unique(ignoreRecord: true)->readOnly()->required(),
                                        Toggle::make('is_active')->default(true)->label('Активный ресурс')->inline(false),
                                    ])
                                ]),
                            Tabs\Tab::make('Содержание ресурса')
                                ->schema([
                                    Repeater::make('content')->label('Ресурсы')->schema([
                                        TextInput::make('title')->label('Главный заголовок столбца')->required(),
                                        Repeater::make('items')->label('Контакты')->schema([
                                            TextInput::make('header')->label('Заголовок')->required(),
                                            Repeater::make('details')->label('Компонент контакта')->schema([
                                                Forms\Components\Grid::make(2)->schema([
                                                    TextInput::make('content')->label('содержание')->required(),
                                                    TextInput::make('url')->label('Ссылка(Необязательно)'),
                                                ]),
                                            ]),
                                        ]),
                                    ])->collapsed()->required(),
                                ]),

                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('title')->label('Название')->sortable()->searchable(),
                TextColumn::make('created_at')->label('Дата создания')->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Активно')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactWidgets::route('/'),
            'create' => Pages\CreateContactWidget::route('/create'),
            'edit' => Pages\EditContactWidget::route('/{record}/edit'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }
}
