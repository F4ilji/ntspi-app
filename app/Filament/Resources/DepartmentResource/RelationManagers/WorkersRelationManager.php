<?php

namespace App\Filament\Resources\DepartmentResource\RelationManagers;

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

    protected static ?string $inverseRelationship = 'departments_work';

    protected static ?string $title = 'Сотрудники кафедры';


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
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('position'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('position')->label('Должность')->required(),
                        Forms\Components\TextInput::make('service_email')->label('Служебная почта'),
                        Forms\Components\TextInput::make('service_phone')->label('Служебный телефон'),
                        Forms\Components\TextInput::make('cabinet')->label('Кабинет'),
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
