<?php

namespace App\Ship\Models;

use App\Containers\User\Models\User;
use App\Ship\Abstracts\Models\UserModel as AbstractUserModel;
use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Tables\Columns\Layout\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class UserModel extends AbstractUserModel
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPanelShield;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        if (config('filament-shield.dashboard_user.enabled', false)) {
            FilamentShield::createRole(name: config('filament-shield.dashboard_user.name', 'dashboard_user'));
            FilamentShield::createRole(name: config('', 'editor'));
            static::created(function (User $user) {
                $user->assignRole(config('filament-shield.dashboard_user.name', 'dashboard_user'));
            });
            static::deleting(function (User $user) {
                $user->assignRole(config('filament-shield.dashboard_user.name', 'dashboard_user'));
            });
        }
    }
    public function canAccessPanel(Panel|\Filament\Panel $panel): bool
    {
        return match ($panel->getId()) {
            "admin" => $this->hasRole(Utils::getSuperAdminName()),
            "dashboard" => $this->hasRole(config('filament-shield.dashboard_user.name', 'dashboard_user'))
                || $this->hasRole(Utils::getSuperAdminName())
                || $this->hasRole(config('filament-shield.invited_user.name', 'invited_user')),
            default => false,
        };
    }
}
