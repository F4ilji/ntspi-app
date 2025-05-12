<?php

namespace App\Filament\Resources\MainSectionResource\RelationManagers;

use App\Containers\AppStructure\Models\SubSection;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SubSectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subSections';

    protected static ?string $inverseRelationship = 'mainSection';

    protected static ?string $title = 'Подразделы';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Название подраздела')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->label('Slug')
                    ->unique(ignoreRecord: true)
                    ->readOnly()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Заголовок подраздела'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('associate')
                    ->label('Прикрепить подраздел')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('recordId')
                            ->label('Подраздел')
                            ->searchable()
                            ->preload()
                            ->options(SubSection::whereNull('main_section_id')->pluck('title', 'id'))
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $subSection = SubSection::find($data['recordId']);
                        $subSection->mainSection()->associate($this->getOwnerRecord());
                        $subSection->save();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('detach')
                    ->label('Открепить')
                    ->color('danger') // Красный цвет
                    ->icon('heroicon-o-x-mark')
                    ->action(function ($record) {
                        $record->mainSection()->dissociate();
                        $record->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\Action::make('bulkDetach')
                        ->label('Открепить выбранные')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->mainSection()->dissociate();
                                $record->save();
                            });
                        }),
                ]),
            ]);
    }
}