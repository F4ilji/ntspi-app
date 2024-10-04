<?php

namespace App\Livewire;

use App\Models\AcceptedInvitation;
use App\Models\Invitation;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Dashboard;
use Filament\Pages\SimplePage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use function Laravel\Prompts\password;

class AcceptInvitation extends SimplePage
{
    use InteractsWithForms;
    use InteractsWithFormActions;

    protected static string $view = 'livewire.accept-invitation';

    public int $invitation;
    private Invitation $invitationModel;

    public ?array $data = [];

    public function mount(): void
    {
        $this->invitationModel = Invitation::findOrFail($this->invitation);

        $this->form->fill([
            'email' => $this->invitationModel->email
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Имя')
                    ->required()
                    ->autofocus(),
                TextInput::make('email')
                    ->disabled(),
                TextInput::make('password')
                    ->label('Пароль')
                    ->password()
                    ->required()
                    ->rule(Password::default())
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->same('passwordConfirmation'),
                TextInput::make('passwordConfirmation')
                    ->label('Подтверждение пароля')
                    ->password()
                    ->required()
                    ->dehydrated(false)
            ])->statePath('data');
    }

    public function create(): void
    {
        DB::transaction(function () {
            $this->invitationModel = Invitation::find($this->invitation);

            $user = User::create([
                'name' => $this->form->getState()['name'],
                'password' => Hash::make($this->form->getState()['password']),
                'email' => $this->invitationModel->email,
            ]);

            AcceptedInvitation::create([
                'sender_id' => $this->invitationModel->user_id,
                'receiver_id' => $user->id,
                'post_limit' => 0
            ]);

            auth()->login($user);

            $this->invitationModel->delete();
        });

        $this->redirect(url(Filament::getPanel('dashboard')->getPath()));
    }

    public function getRegisterFormAction(): Action
    {
        return Action::make('register')
            ->submit('Подтвердить');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getRegisterFormAction()
        ];
    }
}
