<?php

namespace App\Filament\Resources\AcceptedInvitationResource\Pages;

use App\Filament\Resources\AcceptedInvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcceptedInvitation extends EditRecord
{
    protected static string $resource = AcceptedInvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
