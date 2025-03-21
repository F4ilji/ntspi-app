<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VirtualExhibitionResource\Pages;
use App\Filament\Resources\VirtualExhibitionResource\RelationManagers;
use App\Models\VirtualExhibition;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class VirtualExhibitionResource extends Resource
{
    protected static ?string $model = VirtualExhibition::class;

    protected static ?string $navigationGroup = 'Библиотека';

    public static ?string $label = 'Виртуальная выставка';

    protected static ?string $pluralLabel = 'Виртуальные выставки';

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Tabs::make('Tabs')
                            ->tabs([
                                Tabs\Tab::make('Основная информация')
                                    ->schema([
                                        Forms\Components\Grid::make()->schema([
                                            Forms\Components\TextInput::make('title')->required()->label('Заголовок'),
                                            Forms\Components\TextInput::make('category')->required()->label('Категория (Необязательно)'),
                                        ]),
                                        Forms\Components\Textarea::make('preview_text')->required()->label('Текст анонса')
                                            ->required()
                                            ->columnSpanFull(),
                                        Forms\Components\Toggle::make('is_active')->required()->label('Активно')->default(true),
                                    ]),
                                Tabs\Tab::make('Содержание выставки')
                                    ->schema([
                                        \Filament\Forms\Components\Builder::make('content')->label('')->blocks([
                                            Builder\Block::make('heading')->label('Заголовок')
                                                ->schema([
                                                    TextInput::make('id')->hidden()->integer()->default(rand(2335235,324634264263426)),
                                                    TextInput::make('content')
                                                        ->label('')
                                                        ->live(onBlur: true)
                                                        ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set, $get) {
                                                        }),
                                                ]),
                                            Builder\Block::make('paragraph')
                                                ->schema([
                                                    RichEditor::make('content')
                                                        ->toolbarButtons([
                                                            'blockquote',
                                                            'bold',
                                                            'bulletList',
                                                            'italic',
                                                            'link',
                                                            'orderedList',
                                                            'redo',
                                                            'strike',
                                                            'underline',
                                                            'undo',
                                                        ])
                                                        ->label('')
                                                        ->required()

                                                ]),
                                            Builder\Block::make('image')
                                                ->schema([
                                                    FileUpload::make('url')
                                                        ->label('Изображение(-я)')
                                                        ->image()
                                                        ->multiple()
                                                        ->reorderable()
                                                        ->maxFiles(5)
                                                        ->disk('public')
                                                        ->directory('images')
                                                        ->imageEditor()
                                                        ->required(),
                                                    TextInput::make('alt')
                                                        ->label('Описание')
                                                        ->placeholder('Необязяательно')
                                                ])->label('Изображение(-я)'),
                                            Builder\Block::make('video')
                                                ->schema([
                                                    Hidden::make('mime'),

                                                    TextInput::make('title')
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->autofocus(),

                                                    FileUpload::make('path')
                                                        ->required()
                                                        ->acceptedFileTypes(['video/mp4','video/ogg','video/webm'])
                                                        ->maxSize(512000)
                                                        ->disk('videos')
                                                        ->visibility('public')
                                                        ->afterStateUpdated(fn (callable $set, $state) => $set('mime', $state?->getMimeType())),
                                                ]),
                                            Builder\Block::make('files')
                                                ->schema([
                                                    TextInput::make('title')
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->autofocus(),
                                                    FileUpload::make('path')
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
                                                        ->visibility('public')
                                                ])
                                        ])
                                            ->collapsed()
                                            ->blockNumbers(false)
                                            ->collapsible()
                                            ->addActionLabel('Добавить новый блок'),
                                    ]),
                            ]),
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
            'index' => Pages\ListVirtualExhibitions::route('/'),
            'create' => Pages\CreateVirtualExhibition::route('/create'),
            'edit' => Pages\EditVirtualExhibition::route('/{record}/edit'),
        ];
    }
}
