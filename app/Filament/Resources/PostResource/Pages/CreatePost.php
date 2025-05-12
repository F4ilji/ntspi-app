<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Containers\Article\Enums\PostStatus;
use App\Dto\MainSliderDTO;
use App\Filament\Resources\PostResource;
use App\Services\Filament\Domain\Posts\PostDataProcessor;
use App\Services\Filament\Domain\Posts\PostNotificationService;
use App\Services\Filament\Domain\Posts\PostSliderService;
use App\Services\Filament\Domain\Posts\VkPostPublisher;
use App\Services\Filament\Traits\SeoGenerate;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    use SeoGenerate;

    protected static string $resource = PostResource::class;
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
        return (new PostDataProcessor())->processCreate($data);
    }

    protected function afterCreate(): void
    {
        $this->handleSlides();
        $this->createSeo($this->record);
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


    protected function sendNotifications(): void
    {
        (new PostNotificationService())->send($this->record);
    }

    protected function publishToVk(): void
    {
        (new VkPostPublisher())->publish($this->publicationAgreements, $this->record);
    }





}
