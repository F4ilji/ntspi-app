<?php

namespace App\Filament\Resources;

use App\Containers\User\Models\UserDetail;
use App\Filament\Components\Forms\UserDetailForm;
use App\Filament\Resources\UserDetailResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserDetailResource extends Resource
{
    protected static ?string $model = UserDetail::class;

    protected static ?string $navigationGroup = 'Настройки приложения';


    protected static ?string $pluralLabel = 'Доп. Информация';
    protected static ?string $navigationParentItem = 'Польвазователи';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return UserDetailForm::getForm($form);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(),
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
            'index' => Pages\ListUserDetails::route('/'),
            'create' => Pages\CreateUserDetail::route('/create'),
            'edit' => Pages\EditUserDetail::route('/{record}/edit'),
        ];
    }
}
