<?php

namespace App\Filament\Resources\FacultyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkersRelationManager extends RelationManager
{
    protected static string $relationship = 'workers';

    protected static ?string $title = 'Сотрудники';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('position')->label('Должность')->required(),
                Forms\Components\TextInput::make('service_email')->label('Служебная почта'),
                Forms\Components\TextInput::make('service_phone')->label('Служебный телефон'),
                Forms\Components\TextInput::make('cabinet')->label('Кабинет'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Имя'),
                Tables\Columns\TextColumn::make('position')->label('Должность'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect()->preload(),
                        Forms\Components\TextInput::make('position')->label('Должность')->required(),
                        Forms\Components\TextInput::make('service_email')->label('Служебная почта'),
                        Forms\Components\TextInput::make('service_phone')->label('Служебный телефон'),
                        Forms\Components\TextInput::make('cabinet')->label('Кабинет'),
                    ])
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
