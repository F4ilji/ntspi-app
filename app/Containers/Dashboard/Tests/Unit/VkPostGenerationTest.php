<?php

namespace App\Containers\Dashboard\Tests\Unit;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;
use App\Services\Filament\Domain\Posts\VkPostPublisher;
use Tests\TestCase;

/**
 * Тест генерации текста поста для VK
 * 
 * Запуск: docker exec ntspi-php php artisan test --filter=VkPostGenerationTest
 */
class VkPostGenerationTest extends TestCase
{
    /**
     * Тест: генерация текста для поста с файлами
     */
    public function test_vk_post_generation_with_files(): void
    {
        // Создаём тестовый пост с вложениями
        $post = Post::create([
            'title' => 'Тестовая новость с вложениями',
            'slug' => 'test-news-with-files-' . time(),
            'preview_text' => 'Это тестовая новость для проверки генерации VK поста',
            'content' => [
                [
                    'type' => 'paragraph',
                    'data' => [
                        'seo_active' => true,
                        'content' => '<p>Это основной текст новости. Он содержит важную информацию о событии.</p>',
                    ],
                ],
                [
                    'type' => 'heading',
                    'data' => [
                        'content' => '<h2>Детали мероприятия</h2>',
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'data' => [
                        'content' => '<p>Здесь находится подробное описание мероприятия.</p>',
                    ],
                ],
                [
                    'type' => 'files',
                    'data' => [
                        'file' => [
                            [
                                'title' => 'Презентация мероприятия.pdf',
                                'path' => 'media/attachments/presentation.pdf',
                                'expansion' => 'pdf',
                                'size' => '2.5 МиБ',
                            ],
                            [
                                'title' => 'Список участников.xlsx',
                                'path' => 'media/attachments/participants.xlsx',
                                'expansion' => 'xlsx',
                                'size' => '156 КиБ',
                            ],
                        ],
                    ],
                ],
            ],
            'authors' => ['Иванов Иван'],
            'status' => PostStatus::PUBLISHED,
            'publish_at' => now(),
            'images' => [
                'media/image1.jpg',
                'media/image2.png',
            ],
            'preview' => 'media/image1.jpg',
            'user_id' => 1,
            'category_id' => null,
        ]);

        // Генерируем текст для VK
        $publisher = new VkPostPublisher();
        $generatedText = $this->invokePrivateMethod($publisher, 'generateContentForVk', [$post]);

        // Выводим результат для визуальной проверки
        $this->info("\n" . str_repeat('═', 60));
        $this->info('📝 СГЕНЕРИРОВАННЫЙ ТЕКСТ ДЛЯ VK');
        $this->info(str_repeat('═', 60));
        $this->info($generatedText);
        $this->info(str_repeat('═', 60) . "\n");

        // Проверки
        $this->assertStringContainsString('Тестовая новость с вложениями', $generatedText);
        $this->assertStringContainsString('Иванов Иван', $generatedText);
        
        // Проверяем наличие ссылок на файлы в формате VK [url|text]
        $this->assertStringContainsString('📎 Презентация мероприятия.pdf', $generatedText);
        $this->assertStringContainsString('📎 Список участников.xlsx', $generatedText);
        
        // Проверяем формат ссылок VK
        $this->assertMatchesRegularExpression('/\[https?:\/\/[^\|]+\|📎 [^\]]+\]/', $generatedText);

        // Очищаем
        $post->delete();
    }

    /**
     * Тест: генерация текста без файлов
     */
    public function test_vk_post_generation_without_files(): void
    {
        $post = Post::create([
            'title' => 'Новость без вложений',
            'slug' => 'news-without-files-' . time(),
            'preview_text' => 'Новость без прикреплённых файлов',
            'content' => [
                [
                    'type' => 'paragraph',
                    'data' => [
                        'seo_active' => true,
                        'content' => '<p>Текст новости без файлов.</p>',
                    ],
                ],
            ],
            'authors' => ['Петров Пётр'],
            'status' => PostStatus::PUBLISHED,
            'publish_at' => now(),
            'images' => [],
            'preview' => null,
            'user_id' => 1,
            'category_id' => null,
        ]);

        $publisher = new VkPostPublisher();
        $generatedText = $this->invokePrivateMethod($publisher, 'generateContentForVk', [$post]);

        $this->info("\n" . str_repeat('─', 60));
        $this->info('📝 ТЕКСТ БЕЗ ФАЙЛОВ');
        $this->info(str_repeat('─', 60));
        $this->info($generatedText);
        $this->info(str_repeat('─', 60) . "\n");

        $this->assertStringContainsString('Новость без вложений', $generatedText);
        $this->assertStringNotContainsString('Прикреплённые файлы:', $generatedText);

        $post->delete();
    }

    /**
     * Тест: генерация текста с несколькими блоками файлов
     */
    public function test_vk_post_generation_with_multiple_file_blocks(): void
    {
        $post = Post::create([
            'title' => 'Новость с несколькими блоками файлов',
            'slug' => 'news-multiple-files-' . time(),
            'preview_text' => 'Тест с несколькими блоками файлов',
            'content' => [
                [
                    'type' => 'paragraph',
                    'data' => [
                        'content' => '<p>Основной текст.</p>',
                    ],
                ],
                [
                    'type' => 'files',
                    'data' => [
                        'file' => [
                            [
                                'title' => 'Документ 1.docx',
                                'path' => 'media/attachments/doc1.docx',
                                'expansion' => 'docx',
                                'size' => '500 КиБ',
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'data' => [
                        'content' => '<p>Ещё текст.</p>',
                    ],
                ],
                [
                    'type' => 'files',
                    'data' => [
                        'file' => [
                            [
                                'title' => 'Документ 2.pdf',
                                'path' => 'media/attachments/doc2.pdf',
                                'expansion' => 'pdf',
                                'size' => '1.2 МиБ',
                            ],
                            [
                                'title' => 'Документ 3.zip',
                                'path' => 'media/attachments/doc3.zip',
                                'expansion' => 'zip',
                                'size' => '5.6 МиБ',
                            ],
                        ],
                    ],
                ],
            ],
            'authors' => [],
            'status' => PostStatus::PUBLISHED,
            'publish_at' => now(),
            'images' => [],
            'preview' => null,
            'user_id' => 1,
            'category_id' => null,
        ]);

        $publisher = new VkPostPublisher();
        $generatedText = $this->invokePrivateMethod($publisher, 'generateContentForVk', [$post]);

        $this->info("\n" . str_repeat('─', 60));
        $this->info('📝 ТЕКСТ С НЕСКОЛЬКИМИ БЛОКАМИ ФАЙЛОВ');
        $this->info(str_repeat('─', 60));
        $this->info($generatedText);
        $this->info(str_repeat('─', 60) . "\n");

        // Проверяем, что все файлы присутствуют
        $this->assertStringContainsString('📎 Документ 1.docx', $generatedText);
        $this->assertStringContainsString('📎 Документ 2.pdf', $generatedText);
        $this->assertStringContainsString('📎 Документ 3.zip', $generatedText);

        $post->delete();
    }

    /**
     * Тест: проверка формата ссылок VK
     */
    public function test_vk_link_format(): void
    {
        $post = Post::create([
            'title' => 'Проверка формата ссылок',
            'slug' => 'check-link-format-' . time(),
            'content' => [
                [
                    'type' => 'files',
                    'data' => [
                        'file' => [
                            [
                                'title' => 'Файл для проверки.pdf',
                                'path' => 'media/attachments/test.pdf',
                                'expansion' => 'pdf',
                                'size' => '100 КиБ',
                            ],
                        ],
                    ],
                ],
            ],
            'authors' => [],
            'status' => PostStatus::PUBLISHED,
            'publish_at' => now(),
            'images' => [],
            'preview' => null,
            'user_id' => 1,
            'category_id' => null,
        ]);

        $publisher = new VkPostPublisher();
        $generatedText = $this->invokePrivateMethod($publisher, 'generateContentForVk', [$post]);

        // Проверяем точный формат ссылки VK
        $expectedPattern = '/\[https?:\/\/[^\/]+\/storage\/media\/attachments\/test\.pdf\|📎 Файл для проверки\.pdf\]/';
        $this->assertMatchesRegularExpression($expectedPattern, $generatedText);

        $post->delete();
    }

    /**
     * Вызов приватного метода через рефлексию
     */
    private function invokePrivateMethod(object $object, string $methodName, array $parameters = []): mixed
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invoke($object, ...$parameters);
    }
}
