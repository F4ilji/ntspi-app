<?php

namespace App\Filament\Resources\AcceptedInvitationResource\Pages;

use App\Containers\User\Mails\InvitationMail;
use App\Containers\User\Models\Invitation;
use App\Filament\Resources\AcceptedInvitationResource;
use App\Containers\User\Models\User;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Mail;

class ListAcceptedInvitations extends ListRecords
{
    protected static string $resource = AcceptedInvitationResource::class;

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




                })->visible(auth()->user()->can('invite_accepted::invitation'))
        ];
    }
}
