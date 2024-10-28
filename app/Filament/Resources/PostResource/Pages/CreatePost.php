<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Enums\PostStatus;
use App\Filament\Resources\PostResource;
use App\Jobs\CreateVkPost;
use App\Models\Post;
use App\Models\User;
use App\Services\VK\VkService;
use Carbon\Carbon;
use Closure;
use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\AssignOp\Mod;
use VK\Client\VKApiClient;
use VK\OAuth\Scopes\VKOAuthGroupScope;
use VK\OAuth\Scopes\VKOAuthUserScope;
use VK\OAuth\VKOAuth;
use VK\OAuth\VKOAuthDisplay;
use VK\OAuth\VKOAuthResponseType;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected array $seoData;
    protected array $publicationAgreements;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->publicationAgreements = $data['publication'];
        unset($data['publication']);
        $this->seoData = $this->generateSeo($data);
        $data['preview_text'] = $this->setPreviewText($data);
        $data['publish_at'] = $this->setPublishDateTime($data['publish_setting']);
        unset($data['publish_setting']);
        $data['search_data'] = $this->generateSearchData($data['content']);
        $data['reading_time'] = $this->calculateReadingTime($data['search_data']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->seo()->create($this->seoData);
        $this->sendNotify($this->record, auth()->user());
        $this->postToSocialMedia($this->publicationAgreements, $this->record->content, $this->record->title, Carbon::parse($this->record->publish_at)->timestamp);
    }

    private function generateSeo(array $data) : array
    {
        $title = $data['title'];
        $rowData = $this->getBlockBySeoActiveState('paragraph', $data['content']);
        if ($rowData === null) {
            $rowData = $this->getFirstBlockByName('paragraph', $data['content']);
        }
        $description = strip_tags($rowData['data']['content']);
        $image = ($data['preview'] !== null) ? $data['preview'] : null;

        return [
            'title' => $title,
            'description' => Str::limit($description, 160),
            'image' => $image,
        ];
    }

    private function setPreviewText(array $data) : string
    {
        $rowData = $this->getBlockBySeoActiveState('paragraph', $data['content']);
        if ($rowData === null) {
            $rowData = $this->getFirstBlockByName('paragraph', $data['content']);
        }
        $preview_text = strip_tags($rowData['data']['content']);
        return Str::limit($preview_text, 160);
    }

    private function generateSearchData(array $data) : string
    {
        $result = "";
        foreach ($data as $block) {
            $result .= $this->getDataFromBlocks($block);
        }
        // Удаляем лишние пробелы и переносы строк
        $result = preg_replace('/\s+/', ' ', $result);
        $result = trim($result);

        return strtolower($result);
    }
    private function getFirstBlockByName(string $name, array $content) : array|null
    {
        $data = null;
        foreach ($content as $block) {
            $data = ($block['type'] === $name) ? $block : null;
            break;
        }
        return $data;
    }

    private function getBlockBySeoActiveState(string $name, array $content) : array|null
    {
        $data = [];
        foreach ($content as $block) {
            if ($block['type'] === $name) {
                $data[] = $block;
            }
        }
        $block = null;
        foreach ($data as $item) {
            if ($item['data']['seo_active'] === true) {
                $block = $item;
            }
        }
        return $block;
    }

    private function sendNotify($post, $recipient) : void
    {
        Notification::make()
            ->title('Новость на проверку')
            ->body('Новая запись была создана!')
            ->actions([
                Action::make('view')
                    ->label('Проверить')
                    ->button()
                    ->markAsRead()
                    ->url(PostResource::getUrl('edit', ['record' => $post])),

            ])->sendToDatabase($recipient);
    }
    private function setPublishDateTime(array $data) : Carbon|null
    {
        if ($data['publish_after'] === true) {
            return Carbon::parse($data['publish_at']);
        }
        return Carbon::now();
    }
    private function calculateReadingTime(string $text): int
    {

        // Calculate the number of words in the text
        $wordCount = str_word_count($text,0,"АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя");




        // Calculate the average reading speed in words per minute
        $wordsPerMinute = 120; // You can adjust this value based on your desired reading speed

        // Calculate the reading time in minutes
        $readingTime = $wordCount / $wordsPerMinute;

        // Round the reading time to the nearest integer
        $readingTime = round($readingTime);


        return $readingTime;
    }
    protected function convertDataToHtml($blocks) {
        $convertedHtml = "";
        foreach ($blocks as $block) {
            switch ($block['type']) {
                case "header":
                    $convertedHtml .= "<h" . $block['data']['level'] . ">" . $block['data']['text'] . "</h" . $block['data']['level'] . ">";
                    break;
                case "embded":
                    $convertedHtml .= "<div><iframe width='560' height='315' src='" . $block['data']['embed'] . "' frameborder='0' allow='autoplay; encrypted-media' allowfullscreen></iframe></div>";
                    break;
                case "paragraph":
                    $convertedHtml .= "<p>" . $block['data']['text'] . "</p>";
                    break;
                case "delimiter":
                    $convertedHtml .= "<hr />";
                    break;
                case "image":
                    $convertedHtml .= "<img class='img-fluid' src='" . $block['data']['file']['url'] . "' title='" . $block['data']['caption'] . "' /><br /><em>" . $block['data']['caption'] . "</em>";
                    break;
                case "list":
                    $convertedHtml .= "<ul>";
                    foreach ($block['data']['items'] as $li) {
                        $convertedHtml .= "<li>" . $li . "</li>";
                    }
                    $convertedHtml .= "</ul>";
                    break;
                case "table":
                    $convertedHtml .= "<table>";
                    if ($block['data']['withHeadings']) {
                        $convertedHtml .= "<thead><tr>";
                        foreach ($block['data']['content'][0] as $th) {
                            $convertedHtml .= "<th>" . $th . "</th>";
                        }
                        $convertedHtml .= "</tr></thead>";
                    }
                    $convertedHtml .= "<tbody>";
                    foreach ($block['data']['content'] as $row) {
                        $convertedHtml .= "<tr>";
                        foreach ($row as $td) {
                            $convertedHtml .= "<td>" . $td . "</td>";
                        }
                        $convertedHtml .= "</tr>";
                    }
                    $convertedHtml .= "</tbody></table>";
                    break;
                default:
                    echo "Unknown block type " . $block['type'];
                    break;
            }
        }
        return $convertedHtml;
    }
    private function getDataFromBlocks($block) : string
    {
        $data = "";
        switch ($block['type']) {
            case 'paragraph':
                $data .= strip_tags($block['data']['content']) . " ";
                break;
            case 'heading':
                $data .= strip_tags($block['data']['content']) . " ";
                break;
            case 'files':
                foreach ($block['data']['file'] as $file) {
                    $data .= $file['title'] . " ";
                }
                break;
            case 'person':
                $data .= $block['data']['name'] . " ";
                break;
            case 'stepper':
                $data .= $block['data']['step_name'] . " ";
                foreach ($block['data']['steps'] as $step) {
                    $data .= $step['title'] . " ";
                    $data .= strip_tags($step['content']) . " ";
                }
                break;
            case 'tabs':
                foreach ($block['data']['tab'] as $item) {
                    foreach ($item['content'] as $block) {
                        $data .= $this->getDataFromBlocks($block);
                    };
                };
                break;

        }
        return $data;
    }

    private function postToSocialMedia($settings, $content, $title, $publish_date) : void
    {
        if ($this->record->status === PostStatus::PUBLISHED) {
            if ($settings['vk']) {
                $text = "";
                foreach ($content as $block) {
                    $text .= $this->generateContentToVK($block);
                }

                $images = $this->generateImageLinksToVK($this->record->images);

                $post_id = $this->record->id;


                dispatch(new CreateVkPost($title, $text, $images, $post_id, $publish_date));
            }
        }
    }


    private function generateContentToVK($block) : string
    {
        $data = "";

        switch ($block['type']) {
            case 'paragraph':
                // Удаляем все HTML-теги и заменяем закрывающие теги p и h2 на двойной отступ
                $content = preg_replace('/<\/(p|h2)>/', "\n\n", $block['data']['content']);
                $data .= strip_tags($content);
                break;

            case 'heading':
                // Удаляем теги заголовка и добавляем двойной отступ
                $data .= $block['data']['content'] . "\n\n";
                break;
        }

        return $data;
    }
    private function generateImageLinksToVK($images)
    {

        $imageUrls = array_map(function ($file) {
            return url(Storage::url($file)); // Добавляем домен
        }, $images);


        return $imageUrls; // Возвращаем массив с полными URL изображений
    }





}
