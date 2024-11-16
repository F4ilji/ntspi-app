<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcceptedInvitationResource\Pages;
use App\Filament\Resources\AcceptedInvitationResource\RelationManagers;
use App\Models\AcceptedInvitation;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
