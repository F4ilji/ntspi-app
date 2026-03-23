<?php

namespace App\Containers\Dashboard\Data;

/**
 * DTO для представления вложения Email
 */
class EmailAttachmentData
{
    public function __construct(
        public readonly string $filename,
        public readonly string $mimeType,
        public readonly int $size,
        public readonly string $path,
        public readonly ?string $contentId = null,
    ) {}

    /**
     * Создать DTO из файла
     */
    public static function fromFile(string $path, string $originalFilename, string $mimeType, int $size, ?string $contentId = null): self
    {
        return new self(
            filename: $originalFilename,
            mimeType: $mimeType,
            size: $size,
            path: $path,
            contentId: $contentId,
        );
    }

    /**
     * Проверить, является ли вложение изображением
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mimeType, 'image/');
    }

    /**
     * Проверить, является ли вложение документом (DOC/DOCX)
     */
    public function isDocument(): bool
    {
        $documentMimes = [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        $documentExtensions = ['doc', 'docx'];

        return in_array($this->mimeType, $documentMimes, true)
            || in_array(strtolower(pathinfo($this->filename, PATHINFO_EXTENSION)), $documentExtensions, true);
    }

    /**
     * Получить расширение файла
     */
    public function getExtension(): string
    {
        return strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));
    }
}
