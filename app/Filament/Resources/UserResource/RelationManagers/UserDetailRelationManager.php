<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Components\Forms\UserDetailForm;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'userDetail';

    protected static ?string $title = 'Детальная информация';


    protected static ?string $inverseRelationship = 'user';

    public function form(Form $form): Form
    {
        return UserDetailForm::getForm($form);
    }


    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user_id')
            ->columns([
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->visible(!$this->ownerRecord->userDetail()->exists()),
                ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

}
