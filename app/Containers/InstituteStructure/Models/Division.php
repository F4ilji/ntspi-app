<?php

namespace App\Containers\InstituteStructure\Models;

use App\Containers\User\Models\User;
use App\Ship\Contracts\SeoDescriptionInterface;
use App\Ship\Models\Model;
use App\Ship\Traits\HasSeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Division extends Model implements SeoDescriptionInterface
{
    use HasFactory, HasSeo;

    protected $guarded = false;

    protected $casts = [
        'description' => 'array',
    ];

    public function workers()
    {
        return $this->belongsToMany(User::class, 'division_user')->withPivot(['administrativePosition', 'sort', 'service_email', 'service_phone', 'cabinet']);
    }

    public function getSeoDescription(): array
    {
        return $this->description;
    }
}
