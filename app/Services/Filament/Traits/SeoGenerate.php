<?php

namespace App\Services\Filament\Traits;

use App\Services\Filament\Domain\Seo\SeoGeneratorService;
use App\Ship\Contracts\SeoDescriptionInterface;
use App\Ship\Contracts\SeoTitleInterface;

trait SeoGenerate
{
    public function createSeo($record): void
    {
        $seo = $this->generateSeoData($record);
        $record->seo()->create($seo);
    }

    public function updateSeo($record): void
    {
        if ($record->seo()->exists()) {
            $record->seo()->update($this->generateSeoData($record));
        } else {
            $this->createSeo($record);
        }
    }


    private function generateSeoData($record) {
        return app(SeoGeneratorService::class)->generate([
            'title' => $record instanceof SeoTitleInterface
                ? $record->getSeoTitle()
                : $record->title,
            'content' => $record instanceof SeoDescriptionInterface
                ? $record->getSeoDescription()
                : $record->content,
            'preview' => $record->preview,
        ]);
    }
}