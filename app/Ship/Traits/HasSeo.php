<?php

namespace App\Ship\Traits;

use App\Ship\Models\Seo;
use Illuminate\Database\Eloquent\Relations\MorphOne;


trait HasSeo
{
    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    public function prepareDescription(string $description): array
    {
        return [
            [
                'type' => 'paragraph',
                'data' => [
                    'content' => $description,
                ],
            ]
        ];
    }
}