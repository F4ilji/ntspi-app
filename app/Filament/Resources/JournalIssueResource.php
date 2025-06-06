<?php

namespace App\Filament\Resources;

use App\Containers\Science\Models\AcademicJournal;
use App\Containers\Science\Models\JournalIssue;
use App\Filament\Resources\JournalIssueResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JournalIssueResource extends Resource
{

    protected static ?string $navigationGroup = 'Наука';

    public static ?string $label = 'Выпуск';
    protected static ?string $pluralLabel = 'Выпуск журнала';
    protected static ?string $navigationParentItem = 'Научные Журналы';


    protected static ?string $model = JournalIssue::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\Select::make('academic_journal_id')
                            ->options(AcademicJournal::query()->pluck('title', 'id'))
                    ]),
                    FileUpload::make('path_file')
                        ->required()
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            'application/zip'
                        ])
                        ->maxSize(512000)
                        ->disk('public')
                        ->directory('files')
                        ->downloadable()
                        ->visibility('public'),
                    Forms\Components\TextInput::make('year_publication')->integer(),
                    Toggle::make('is_active')->default(true)->label('Активный выпуск')->inline(false),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListJournalIssues::route('/'),
            'create' => Pages\CreateJournalIssue::route('/create'),
            'edit' => Pages\EditJournalIssue::route('/{record}/edit'),
        ];
    }
}
