<?php

namespace App\Filament\Resources;

use App\Containers\User\Models\AcceptedInvitation;
use App\Filament\Resources\AcceptedInvitationResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class AcceptedInvitationResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = AcceptedInvitation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static ?string $label = 'Принятое приглашение';

    protected static ?string $pluralLabel = 'Принятные приглашения';

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
                Tables\Columns\TextColumn::make('receiver.name')->label('Приглашенный пользователь'),
                TextInputColumn::make('post_limit')->label('Лимит постов')->default(0)->rules(['required', 'max:10', 'integer'])
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
            'index' => Pages\ListAcceptedInvitations::route('/'),
            'create' => Pages\CreateAcceptedInvitation::route('/create'),
            'edit' => Pages\EditAcceptedInvitation::route('/{record}/edit'),
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
            'invite'
        ];
    }
}
