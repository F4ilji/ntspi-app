<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Dto\MainSliderDTO;
use App\Enums\PostStatus;
use App\Filament\Resources\PostResource;
use App\Services\Filament\Domain\Posts\PostDataProcessor;
use App\Services\Filament\Domain\Posts\PostNotificationService;
use App\Services\Filament\Domain\Posts\PostSeoGenerator;
use App\Services\Filament\Domain\Posts\PostSliderService;
use App\Services\Filament\Domain\Posts\VkPostPublisher;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected array $seoData;
    protected array $publicationAgreements;

    protected array $slideData;

    protected static array|string $routeMiddleware = ['limit.post'];


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->extractAdditionalData($data);
        return $this->processPostData($data);
    }

    protected function extractAdditionalData(array &$data): void
    {
        $this->publicationAgreements = $data['publication'] ?? [];
        $this->slideData = $data['slide'] ?? [];
        unset($data['slide'], $data['publication']);
    }

    protected function processPostData(array $data): array
    {
        return (new PostDataProcessor())->process($data);
    }

    protected function afterCreate(): void
    {
        $this->handleSlides();
        $this->generateSeo();
        $this->sendNotifications();
        $this->publishToVk();
    }

    protected function handleSlides(): void
    {
        if (empty($this->slideData)) {
            return;
        }

        $this->slideData['is_active'] = $this->record->status === PostStatus::PUBLISHED;
        $this->slideData['start_time'] = $this->record->publish_at;

        $sliderDTO = MainSliderDTO::fromArray($this->slideData);
        (new PostSliderService($sliderDTO, $this->record))->create();
    }

    protected function generateSeo(): void
    {
        $seoData = (new PostSeoGenerator())->generate([
            'title' => $this->record->title,
            'content' => $this->record->content,
            'preview' => $this->record->preview,
        ]);
        $this->record->seo()->create($seoData);
    }

    protected function sendNotifications(): void
    {
        (new PostNotificationService())->send($this->record);
    }

    protected function publishToVk(): void
    {
        (new VkPostPublisher())->publish($this->publicationAgreements, $this->record);
    }





}
