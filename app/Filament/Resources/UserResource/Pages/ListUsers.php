<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Mail;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('inviteUser')->label('Пригласить автора')
                ->form([
                    TextInput::make('email')
                        ->email()
                        ->label('Почта для письма')
                        ->required()
                ])
                ->action(function ($data) {
                    $inv = User::query()
                        ->where('email', $data['email'])
                        ->first();

                    if ($inv) {
                        Notification::make()
                            ->title('Данный пользователь существует в системе')
                            ->danger()
                            ->send();
                    } else {
                        $invitation = Invitation::create([
                            'email' => $data['email'],
                            'user_id' => auth()->user()->id,
                        ]);
                        Mail::to($invitation->email)->send(new InvitationMail($invitation));
                        Notification::make('invitedSuccess')
                            ->body('Пользователь приглашен')
                            ->success()->send();
                    }




                })->visible(auth()->user()->can('invite_user'))
        ];
    }
}
