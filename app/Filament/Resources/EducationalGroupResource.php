<?php

namespace App\Filament\Resources;

use App\Enums\FormEducation;
use App\Filament\Resources\EducationalGroupResource\Pages;
use App\Filament\Resources\EducationalGroupResource\RelationManagers;
use App\Models\EducationalGroup;
use App\Models\Faculty;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EducationalGroupResource extends Resource
{
    protected static ?string $navigationGroup = 'Расписание и группы';

    protected static ?string $model = EducationalGroup::class;

    protected static ?string $pluralLabel = 'Группы';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('title')->label('Название группы')->required(),
                        Forms\Components\Select::make('faculty_id')->label('Факультет')->required()
                            ->options(Faculty::all()->pluck('title', 'id')),
                        Forms\Components\Select::make('education_form_id')->label('Форма обучения')
                            ->options(FormEducation::class)
                    ]),
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
                Tables\Columns\BadgeColumn::make('faculty.title')->label('Категория')->sortable(),
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
            'index' => Pages\ListEducationalGroups::route('/'),
            'create' => Pages\CreateEducationalGroup::route('/create'),
            'edit' => Pages\EditEducationalGroup::route('/{record}/edit'),
        ];
    }
}
