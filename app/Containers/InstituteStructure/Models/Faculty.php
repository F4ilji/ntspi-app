<?php

namespace App\Containers\InstituteStructure\Models;

use App\Containers\User\Models\User;
use App\Ship\Models\Model;
use App\Ship\Traits\HasSeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faculty extends Model
{
    use HasFactory, HasSeo;

    protected $guarded = false;

    protected $casts = [
        'content' => 'array',
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'workers_faculties')->withPivot(['position', 'sort', 'service_email', 'service_phone', 'cabinet'])->whereHas('userDetail');
    }
}
