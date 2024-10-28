<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageReferenceListResource\Pages;
use App\Filament\Resources\PageReferenceListResource\RelationManagers;
use App\Models\Event;
use App\Models\Page;
use App\Models\PageReferenceList;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PageReferenceListResource extends Resource
{
    protected static ?string $model = PageReferenceList::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')->schema([
                    Tabs::make('Tabs')
                        ->tabs([
                            Tabs\Tab::make('Основная информация')
                                ->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        TextInput::make('title')->label('Название ресурса')->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                                $set('slug', Str::slug($state));
                                                $set('seo.title', $state);
                                            }),
                                        TextInput::make('slug')->label('Slug')->unique(ignoreRecord: true)->readOnly()->required(),
                                        Toggle::make('is_active')->default(true)->label('Активный ресурс')->inline(false),
                                    ])
                                ]),
                            Tabs\Tab::make('Содержание новости')
                                ->schema([
                                    Repeater::make('content')->schema([
                                        Forms\Components\Section::make('Быстрая настройка ресурса')->schema([
                                            Forms\Components\Grid::make()->schema([
                                                Forms\Components\Select::make('model_select')
                                                    ->name('')
                                                    ->label('Выбор типа данных')
                                                    ->options([
                                                        'Post' => 'Новость',
                                                        'Page' => 'Страница',
                                                        'Event' => 'Мероприятие',
                                                        'Custom' => 'Кастомная ссылка',
                                                    ])
                                                    ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set, Forms\Get $get) {
                                                        if ($get('model_select') === 'Custom') {
                                                            $set('model', null);
                                                            $set('title', null);
                                                            $set('content', null);
                                                            $set('link', null);
                                                        };
                                                    })->live(onBlur: true),

                                                Forms\Components\Select::make('model')
                                                    ->label('Поиск данных')
                                                    ->name('')
                                                    ->live(onBlur: true)
                                                    ->searchable()
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set, Forms\Get $get) {
                                                        if ($get('model_select') === 'Post') {
                                                            $post = Post::find($state);
                                                            $set('title', $post->title);
                                                            $relativeUrl = parse_url(route('client.post.show', $post->slug), PHP_URL_PATH);
                                                            $set('link', $relativeUrl);
                                                        };
                                                        if ($get('model_select') === 'Page') {
                                                            $page = Page::find($state);
                                                            $set('title', $page->title);
                                                            $set('link', $page->path);
                                                        };
                                                        if ($get('model_select') === 'Event') {
                                                            $event = Event::find($state);
                                                            $set('title', $event->title);
                                                            $relativeUrl = parse_url(route('client.event.show', $event->slug), PHP_URL_PATH);
                                                            $set('link', $relativeUrl);
                                                        };

                                                    })
                                                    ->options(function (Forms\Get $get) {
                                                        if ($get('model_select') === 'Post') {
                                                            return Post::where('status', '=', 'published')->pluck('title', 'id');
                                                        };
                                                        if ($get('model_select') === 'Page') {
                                                            return Page::where('title', '!=', null)->pluck('title', 'id');
                                                        };
                                                        if ($get('model_select') === 'Event') {
                                                            return Event::all()->pluck('title', 'id');
                                                        };
                                                        if ($get('model_select') === 'Custom') {
                                                            return [];
                                                        };
                                                    }),
                                            ]),
                                        ]),
                                        TextInput::make('title')->label('Title')->required(),
                                        FileUpload::make('image')
                                            ->label('Изображение')
                                            ->image()
                                            ->optimize('webp')
                                            ->resize(50)
                                            ->disk('public')
                                            ->directory('images')
                                            ->imageEditor(),
                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('link')
                                                ->label('Ссылка ресурса')
                                                ->required(),
                                            Forms\Components\TextInput::make('link_text')
                                                ->default('Читать')
                                                ->label('Текст кнопки')
                                                ->required(),
                                        ]),
                                    ])->collapsed()->required(),
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
            'index' => Pages\ListPageReferenceLists::route('/'),
            'create' => Pages\CreatePageReferenceList::route('/create'),
            'edit' => Pages\EditPageReferenceList::route('/{record}/edit'),
        ];
    }
}
