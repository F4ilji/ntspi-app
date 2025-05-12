<?php

namespace App\Filament\Resources;


use App\Containers\AppStructure\Models\Page;
use App\Filament\Components\Forms\PageForm;
use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers\SectionRelationManager;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $pluralLabel = 'Страницы';


    protected static ?string $navigationGroup = 'Структура приложения';

    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return PageForm::getForm($form);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('path')->label('Путь')
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_registered')->getStateUsing(function ($record) {
                    return ($record->is_registered) ? 'true' : 'false';
                })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'false' => 'gray',
                        'true' => 'success',
                    })
                ->label('Зарезервированная страница'),
                Tables\Columns\TextColumn::make('is_url')->getStateUsing(function ($record) {
                    return ($record->is_url) ? 'true' : 'false';
                })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'false' => 'gray',
                        'true' => 'success',
                    })
                    ->label('Ссылка на другой ресурс'),
                Tables\Columns\TextColumn::make('code')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '200' => 'success',
                        '404' => 'warning',
                        '500' => 'danger',
                    })
                    ->label('Код страницы'),


            ])
            ->filters([
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
            SectionRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
