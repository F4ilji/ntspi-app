<?php

namespace App\Filament\Resources\FacultyRecourceResource\RelationManagers;

use App\Models\Faculty;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class DepartmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'departments';

    protected static ?string $title = 'Кафедры';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->label('Название кафедры')->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')->label('Slug')->unique(ignoreRecord: true)->readOnly()->required(),
                Toggle::make('is_active')->default(true)->label('Активная кафедра')->inline(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Заголовок'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
}
