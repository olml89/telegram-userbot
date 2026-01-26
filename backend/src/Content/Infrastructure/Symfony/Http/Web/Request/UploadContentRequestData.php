<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Web\Request;

use olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Form\ContentType;
use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Http\Web\Request\RequestData;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

final class UploadContentRequestData implements RequestData
{
    #[Assert\NotBlank(message: 'Name is required')]
    #[Assert\Length(min: 3, max: 255)]
    public ?string $name = null;

    #[Assert\Length(max: 1000)]
    public ?string $description = null;

    #[Assert\NotNull(message: 'Please upload a media file')]
    #[Assert\File(
        maxSize: '2048M',
        mimeTypes: [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'video/mp4',
            'video/mpeg',
        ],
        maxSizeMessage: 'The file is too large (max 2GB)',
        mimeTypesMessage: 'Please upload a valid media file (image or video)',
    )]
    public ?UploadedFile $file = null;

    /** @var string[] */
    #[Assert\Count(
        min: 1,
        max: 20,
        minMessage: 'Please add at least one tag',
        maxMessage: 'You cannot add more than 20 tags',
    )]
    public array $tags = [];

    /**
     * @return class-string<ContentType>
     */
    public function getType(): string
    {
        return ContentType::class;
    }
}
