<?php

namespace App\Filament\Resources;

use App\Containers\Widget\Models\CustomForm;
use App\Filament\Components\Forms\CustomFormForm;
use App\Filament\Resources\CustomFormResource\Pages;
use App\Filament\Resources\CustomFormResource\RelationManagers\ResponsesRelationManager;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomFormResource extends Resource
{

    public static ?string $label = 'Форма';
    protected static ?string $pluralLabel = 'Пользовательские формы';

    protected static ?string $navigationGroup = 'Виджеты';



    protected static ?string $model = CustomForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    public static function form(Form $form): Form
    {
        return CustomFormForm::getForm($form);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Название')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('form_id')->label('Индефикатор формы'),
                Tables\Columns\TextColumn::make('status')->label('Статус')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
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
            ResponsesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomForms::route('/'),
            'create' => Pages\CreateCustomForm::route('/create'),
            'edit' => Pages\EditCustomForm::route('/{record}/edit'),
        ];
    }
}
