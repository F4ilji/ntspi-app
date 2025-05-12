<?php

namespace App\Ship\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seo extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];

    public function seoable()
    {
        return $this->morphTo();
    }
}
