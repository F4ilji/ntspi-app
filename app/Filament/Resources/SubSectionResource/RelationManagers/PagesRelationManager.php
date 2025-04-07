<?php

namespace App\Filament\Resources\SubSectionResource\RelationManagers;

use App\Filament\Components\Forms\PageForm;
use App\Models\Page;
use App\Models\SubSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PagesRelationManager extends RelationManager
{
    protected static string $relationship = 'pages';

    protected static ?string $title = 'Страницы';

    public function form(Form $form): Form
    {
        return PageForm::getForm($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('associate')
                    ->label('Прикрепить страницу')
                    ->color('success')
                    ->button()
                    ->form([
                        Forms\Components\Select::make('recordId')
                            ->label('Страница')
                            ->searchable()
                            ->preload()
                            ->options(Page::whereNull('sub_section_id')->whereNotNull('title')->pluck('title', 'id'))
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $page = Page::find($data['recordId']);
                        $page->section()->associate($this->getOwnerRecord());
                        $page->save(); // Вызовет наблюдатели
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('detach')
                    ->label('Открепить')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->action(function (Page $record) {
                        $record->section()->dissociate();
                        $record->save(); // Вызовет наблюдатели
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\Action::make('bulkDetach')
                        ->label('Открепить выбранные')
                        ->color('danger')
                        ->icon('heroicon-o-x-mark')
                        ->action(function ($records) {
                            $records->each(function (Page $record) {
                                $record->section()->dissociate();
                                $record->save(); // Вызовет наблюдатели для каждой записи
                            });
                        }),
                ]),
            ]);
    }
}