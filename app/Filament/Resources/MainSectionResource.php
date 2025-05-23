<?php

namespace App\Filament\Resources;

use App\Containers\AppStructure\Models\MainSection;
use App\Filament\Resources\MainSectionResource\Pages;
use App\Filament\Resources\MainSectionResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class MainSectionResource extends Resource
{
    protected static ?string $model = MainSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    public static ?string $label = 'Главный раздел';

    public static ?string $pluralLabel = 'Главные разделы';






    protected static ?string $navigationGroup = 'Структура приложения';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\TextInput::make('title')->label('Название раздела')->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                $set('slug', Str::slug($state));
                            }),
                        TextInput::make('slug')->label('Текстовый идентификатор раздела')->unique(ignoreRecord: true)->readOnly()->required(),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Tables\Columns\TextColumn::make('title')
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
            RelationManagers\SubSectionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMainSections::route('/'),
            'create' => Pages\CreateMainSection::route('/create'),
            'edit' => Pages\EditMainSection::route('/{record}/edit'),
        ];
    }


}
