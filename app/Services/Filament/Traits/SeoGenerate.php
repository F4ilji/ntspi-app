<?php

namespace App\Services\Filament\Traits;

use App\Services\App\Seo\SeoDescriptionInterface;
use App\Services\Filament\Domain\Seo\SeoGeneratorService;

trait SeoGenerate
{
    public function createSeo($record): void
    {
        $record->seo()->create($this->generateSeo($record));
    }

    public function updateSeo($record): void
    {
        if ($record->seo()->exists()) {
            $record->seo()->update($this->generateSeo($record));
        } else {
            $this->createSeo($record);
        }
    }


    private function generateSeo($record) {
        return app(SeoGeneratorService::class)->generate([
            'title' => $record->title,
            'content' => $record instanceof SeoDescriptionInterface
                ? $record->getSeoDescription()
                : $record->content,
            'preview' => $record->preview,
        ]);
    }
}