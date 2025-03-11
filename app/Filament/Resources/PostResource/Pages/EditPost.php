<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Dto\MainSliderDTO;
use App\Enums\PostStatus;
use App\Filament\Resources\PostResource;
use App\Models\Post;
use App\Services\Filament\Domain\Posts\PostDataProcessor;
use App\Services\Filament\Domain\Posts\PostNotificationService;
use App\Services\Filament\Domain\Posts\PostSeoGenerator;
use App\Services\Filament\Domain\Posts\PostSliderService;
use App\Services\Filament\Domain\Posts\VkPostPublisher;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected array $seoData;
    protected array $publicationAgreements;

    protected array $slideData;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $post = Post::query()->with(['seo', 'mainSlider'])->find($data['id']);
        $data['slide'] = $post->mainSlider->toArray() ?? null;
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function afterSave(): void
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
        (new PostSliderService($sliderDTO, $this->record))->update();
    }

    protected function generateSeo(): void
    {
        $seoData = (new PostSeoGenerator())->generate([
            'title' => $this->record->title,
            'content' => $this->record->content,
            'preview' => $this->record->preview,
        ]);
        $this->record->seo()->update($seoData);
    }

    protected function sendNotifications(): void
    {
        // Отправляем уведомления
        $notificationService = new PostNotificationService();
        if ($this->record->status === PostStatus::PUBLISHED) {
            $notificationService->sendSuccessNotification($this->record);
        } elseif ($this->record->status === PostStatus::REJECTED) {
            $notificationService->sendDeniedNotification($this->record);
        }
    }

    protected function publishToVk(): void
    {
        (new VkPostPublisher())->publish($this->publicationAgreements, $this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
