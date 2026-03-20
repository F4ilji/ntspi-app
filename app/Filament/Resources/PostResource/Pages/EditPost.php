<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;
use App\Dto\MainSliderDTO;
use App\Filament\Resources\PostResource;
use App\Services\Filament\Domain\Posts\PostDataProcessor;
use App\Services\Filament\Domain\Posts\PostNotificationService;
use App\Services\Filament\Domain\Posts\PostSliderService;
use App\Services\Filament\Domain\Posts\VkPostPublisher;
use App\Services\Filament\Domain\Seo\SeoGeneratorService;
use App\Services\Filament\Traits\SeoGenerate;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditPost extends EditRecord
{
    use SeoGenerate;

    protected static string $resource = PostResource::class;

    protected array $publicationAgreements;

    protected array $slideData;


    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($data['publish_at']) {
            $data['publish_setting']['publish_after'] = true;
            $data['publish_setting']['publish_at'] = $data['publish_at'];
        }
        $data['publication']['vk'] = true;
        $data['publication']['telegram'] = true;
        $post = Post::query()->with(['seo', 'slide'])->find($data['id']);
        $data['slide'] = $post->slide ? $post->slide->toArray() : null;
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['publish_at'] = $this->record->publish_at;
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
        return (new PostDataProcessor())->processUpdate($data);
    }

    protected function afterSave(): void
    {
        $this->handleSlides();
        $this->updateSeo($this->record);
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
        $seoData = app(SeoGeneratorService::class)->generate([
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
        $postRelation = DB::table('posts_vk_posts')->select()->where('post_id', $this->record->id)->first();
        if($postRelation){
            (new VkPostPublisher())->update($this->publicationAgreements, $this->record);
        } else {
            (new VkPostPublisher())->publish($this->publicationAgreements, $this->record);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
