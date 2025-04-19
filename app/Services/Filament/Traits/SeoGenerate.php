<?php

namespace App\Services\Filament\Traits;

use App\Services\App\Seo\SeoDescriptionInterface;
use App\Services\Filament\Domain\Seo\SeoGeneratorService;

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
            'title' => $record->title,
            'content' => $record instanceof SeoDescriptionInterface
                ? $record->getSeoDescription()
                : $record->content,
            'preview' => $record->preview,
        ]);
    }
}