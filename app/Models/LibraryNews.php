<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryNews extends Model
{
    use HasFactory;

    protected $guarded = false;

    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    protected $casts = [
        'content' => 'array',
    ];
}
