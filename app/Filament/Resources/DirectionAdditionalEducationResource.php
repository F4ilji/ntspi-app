<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DirectionAdditionalEducationResource\Pages;
use App\Filament\Resources\DirectionAdditionalEducationResource\RelationManagers;
use App\Models\DirectionAdditionalEducation;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class DirectionAdditionalEducationResource extends Resource
{
    protected static ?string $model = DirectionAdditionalEducation::class;

    protected static ?string $navigationGroup = 'Образование';

    public static ?string $label = 'Направление';
    protected static ?string $pluralLabel = 'Направления дополнительного образования';
    protected static ?string $navigationParentItem = 'Дополнительное Образование';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Grid::make()->schema([
                        TextInput::make('title')->label('Заголовок')->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                $set('slug', Str::slug($state));
                                $set('seo.title', $state);
                            }),
                        TextInput::make('slug')->label('Slug')->unique(ignoreRecord: true)->readOnly()->required(),
                    ]),
                    Forms\Components\Toggle::make('is_active')->label('Активно')->columnSpanFull()->inline(false)->default(true),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('title')->label('Название')->sortable()->searchable(),
                TextColumn::make('created_at')->label('Дата создания')->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Активно')->sortable(),
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
            'index' => Pages\ListDirectionAdditionalEducation::route('/'),
            'create' => Pages\CreateDirectionAdditionalEducation::route('/create'),
            'edit' => Pages\EditDirectionAdditionalEducation::route('/{record}/edit'),
        ];
    }
}
