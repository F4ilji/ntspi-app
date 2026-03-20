<?php

namespace App\Services\Filament\Domain\Posts;

use App\Containers\Article\Models\Post;
use App\Containers\User\Models\Role;
use App\Containers\User\Models\User;
use App\Filament\Resources\PostResource;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class PostNotificationService
{
    /**
     * Отправляет уведомления редакторам о новом посте.
     *
     * @param Post $post
     * @return void
     */
    public function send(Post $post): void
    {
        // Находим всех пользователей с ролью "editor"
        $editors = Role::findByName('editor')->users;

        // Отправляем уведомление каждому редактору
        foreach ($editors as $editor) {
            Notification::make()
                ->title('Новость на проверку')
                ->body('Новость "' . $post->title . '" нуждается в проверке')
                ->actions([
                    Action::make('view')
                        ->label('Проверить')
                        ->button()
                        ->markAsRead()
                        ->url(PostResource::getUrl('edit', ['record' => $post])),
                ])
                ->sendToDatabase($editor); // Отправляем уведомление конкретному редактору
        }
    }

    public function sendSuccessNotification(Post $post): void
    {
        $user = User::find($post->user_id);

        Notification::make()
            ->title('Ваша новость опубликована')
            ->body('Новость "' . $post->title . '" опубликована')
            ->actions([
                Action::make('view')
                    ->label('Смотреть')
                    ->button()
                    ->markAsRead()
                    ->url(route('client.post.show', $post->slug)),
            ])
            ->sendToDatabase($user);
    }

    /**
     * Отправляет уведомление об отклонении.
     *
     * @param Post $post
     * @return void
     */
    public function sendDeniedNotification(Post $post): void
    {
        $user = User::find($post->user_id);

        Notification::make()
            ->title('Ваша новость отклонена :(')
            ->body('Новость "' . $post->title . '" была отклонена')
            ->actions([
                Action::make('view')
                    ->label('Смотреть')
                    ->button()
                    ->markAsRead()
                    ->url(PostResource::getUrl('edit', ['record' => $post])),
            ])
            ->sendToDatabase($user);
    }
}