<?php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use HasFactory;

    protected $guarded = false;

    public function section() : BelongsTo
    {
        return $this->belongsTo(SubSection::class, 'sub_section_id');
    }

    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    public function getBreadcrumbs(string $url) : array
    {
        $path = ltrim(parse_url(route($url), PHP_URL_PATH), '/');

        $page = self::where('path', '=', $path)->with('section.pages.section', 'section.mainSection')->first();

        dd($page);
    }

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
    ];

}
