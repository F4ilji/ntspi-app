<?php

namespace App\Models;

use App\Services\App\Seo\SeoDescriptionInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model implements SeoDescriptionInterface
{
    use HasFactory;

    protected $guarded = false;

    protected $casts = [
        'description' => 'array',
    ];

    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    public function workers()
    {
        return $this->belongsToMany(User::class, 'division_user')->withPivot(['administrativePosition', 'sort', 'service_email', 'service_phone', 'cabinet']);
    }

    public function getSeoDescription(): array
    {
        return $this->description;
    }
}
