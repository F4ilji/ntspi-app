<?php

namespace App\Filament\Resources;

use App\Containers\Article\Models\Post;
use App\Filament\Components\Forms\PostForm;
use App\Filament\Resources\PostResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationGroup = 'Новости и мероприятия';

    public static ?string $label = 'Новость';

    protected static ?string $pluralLabel = 'Новости';

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    public static function form(Form $form): Form
    {
        return PostForm::getForm($form);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Дата создания')->sortable(),
                Tables\Columns\TextColumn::make('title')->label('Заголовок')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('status')->label('Статус')->sortable()->badge(),
                Tables\Columns\TextColumn::make('publish_at')->label('Дата публикации')->sortable(),
                Tables\Columns\TextColumn::make('author.name')->label('Автор')->sortable()->searchable(),

            ])->defaultSort('publish_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
            'view' => Pages\ViewPost::route('/{record}'),

        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'publish',
            'view_only_own_records'
        ];
    }




}
