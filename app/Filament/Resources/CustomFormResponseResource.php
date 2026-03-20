<?php

namespace App\Filament\Resources;

use App\Containers\Widget\Models\CustomFormResponse;
use App\Filament\Resources\CustomFormResponseResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomFormResponseResource extends Resource
{
    public static ?string $label = 'Форма';
    protected static ?string $pluralLabel = 'Ответы на формы';
    protected static ?string $navigationParentItem = 'nobody';

    protected static ?string $model = CustomFormResponse::class;


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListCustomFormResponses::route('/'),
            'create' => Pages\CreateCustomFormResponse::route('/create'),
            'edit' => Pages\EditCustomFormResponse::route('/{record}/edit'),
        ];
    }
}
