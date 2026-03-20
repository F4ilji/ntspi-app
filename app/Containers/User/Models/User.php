<?php

namespace App\Containers\User\Models;

use App\Containers\InstituteStructure\Models\Department;
use App\Containers\InstituteStructure\Models\Division;
use App\Containers\InstituteStructure\Models\Faculty;
use App\Ship\Contracts\SeoDescriptionInterface;
use App\Ship\Contracts\SeoTitleInterface;
use App\Ship\Traits\HasSeo;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Tables\Columns\Layout\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, SeoTitleInterface, SeoDescriptionInterface
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPanelShield, HasSeo;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'slug',
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
        'password' => 'hashed',
    ];

    public function userDetail(): HasOne
    {
        return $this->hasOne(UserDetail::class, 'user_id');
    }


    public function departments_work(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'workers_departments')->withPivot(['position']);
    }

    public function departments_teach(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'teachers_departments')->withPivot(['teaching_position']);
    }

    public function divisions(): BelongsToMany
    {
        return $this->belongsToMany(Division::class, 'division_user')->withPivot(['administrativePosition']);
    }


    public function faculties(): BelongsToMany
    {
        return $this->belongsToMany(Faculty::class, 'workers_faculties')->withPivot(['position']);
    }

    // Отношение к отправленным приглашениям
    public function sentInvitations(): HasMany
    {
        return $this->hasMany(AcceptedInvitation::class, 'sender_id');
    }

    // Отношение к полученным приглашениям
    public function receivedInvitation(): HasOne
    {
        return $this->hasOne(AcceptedInvitation::class, 'receiver_id');
    }


    public function canAccessPanel(Panel|\Filament\Panel $panel): bool
    {
        return match ($panel->getId()) {
            "admin", "dashboard" => $this->hasRole(config('filament-shield.dashboard_user.name', 'dashboard_user'))
                || $this->hasRole(Utils::getSuperAdminName())
                || $this->hasRole(config('filament-shield.invited_user.name', 'invited_user')),
            default => false,
        };
    }

    public function getSeoTitle(): string
    {
        return $this->name;
    }

    public function getSeoDescription(): array
    {
        $text = 'Персональная страница сотрудника - ' . $this->name;
        return $this->prepareDescription($text);
    }
}
