<?php

namespace App\Containers\Dashboard\Actions\Users;

use App\Containers\User\Mails\InvitationMail;
use App\Containers\User\Models\Invitation;

class InviteUserAction
{
    public function run(string $email, int $senderId): array
    {
        // Проверяем существует ли пользователь
        $existingUser = \App\Containers\User\Models\User::where('email', $email)->first();

        if ($existingUser) {
            return [
                'success' => false,
                'message' => 'Данный пользователь уже существует в системе',
            ];
        }

        // Создаем приглашение
        $invitation = Invitation::create([
            'email' => $email,
            'user_id' => $senderId,
        ]);

        // Отправляем письмо
        \Illuminate\Support\Facades\Mail::to($invitation->email)->send(new InvitationMail($invitation));

        return [
            'success' => true,
            'message' => 'Пользователь успешно приглашен',
            'invitation' => $invitation,
        ];
    }
}
