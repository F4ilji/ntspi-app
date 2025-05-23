<?php

namespace App\Filament\Resources;

use App\Containers\AppStructure\Models\Page;
use App\Filament\Resources\UrlLinkResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UrlLinkResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $pluralLabel = 'Ссылка';

    protected static ?string $navigationParentItem = 'Страницы';

    protected static ?string $navigationGroup = 'Структура приложения';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            TextInput::make('title')->label('Заголовок')->required(),
                            TextInput::make('path')->label('URL')->unique(ignoreRecord: true)->required()
                        ]),
                        Select::make('sub_section_id')->label('Подраздел')
                            ->relationship('section', 'title'),
                        Toggle::make('searchable')->default(true)->label('Индексируется поиском')->inline(false)


                    ])
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUrlLinks::route('/'),
            'create' => Pages\CreateUrlLink::route('/create'),
            'edit' => Pages\EditUrlLink::route('/{record}/edit'),
        ];
    }
}
