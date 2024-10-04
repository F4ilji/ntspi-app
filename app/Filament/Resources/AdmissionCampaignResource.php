<?php

namespace App\Filament\Resources;

use App\Enums\FormEducation;
use App\Enums\LevelEducational;
use App\Filament\Resources\AdmissionCampaignResource\Pages;
use App\Filament\Resources\AdmissionCampaignResource\RelationManagers;
use App\Models\AdmissionCampaign;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdmissionCampaignResource extends Resource
{
    protected static ?string $model = AdmissionCampaign::class;

    protected static ?string $navigationGroup = 'Образование';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $pluralLabel = 'Приемная-компания';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Название')->required()->columnSpanFull(),
                Forms\Components\Select::make('academic_year')->label('Академический год')->required()
                    ->options(['2024' => '2024/2025', '2025' => '2025/2026']),
                Forms\Components\Select::make('status')->label('Статус')->required()
                    ->options(['1' => 'Активный', '2' => 'Архивный', '3' => 'Скрыт']),
                Forms\Components\Section::make()->schema([
                    Forms\Components\Repeater::make('info')->schema([
                        Forms\Components\Select::make('edu_name')->options(LevelEducational::class),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Section::make()->schema([
                                TextInput::make('total_programs')->label('Количество программ по набору')->integer()->required(),
                            ]),
                            Forms\Components\Section::make('Места')->schema([
                                TextInput::make('och_count')->label('Количество мест (Очная форма)')->integer()->required(),
                                TextInput::make('zaoch_count')->label('Количество мест (Заочная форма)')->integer()->required(),
                                TextInput::make('budget_places')->label('Количество бюджетных мест')->integer()->required(),
                                TextInput::make('non_budget_places')->label('Количество платных мест')->integer()->required(),
                            ]),
                        ]),
                    ])->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('academic_year'),
                Tables\Columns\TextColumn::make('status'),
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
            'index' => Pages\ListAdmissionCampaigns::route('/'),
            'create' => Pages\CreateAdmissionCampaign::route('/create'),
            'edit' => Pages\EditAdmissionCampaign::route('/{record}/edit'),
        ];
    }
}
