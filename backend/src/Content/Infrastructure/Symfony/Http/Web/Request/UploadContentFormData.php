<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Web\Request;

use olml89\TelegramUserbot\Backend\Content\Application\UploadContentCommand;
use olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Form\ContentType;
use olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\UploadedFile\SymfonyUploadedFile;
use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Http\Web\Request\FormData;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Validator;
use Webmozart\Assert\Assert;

/**
 * @implements FormData<UploadContentCommand>
 */
final class UploadContentFormData implements FormData
{
    #[Validator\NotBlank(message: 'Name is required')]
    #[Validator\Length(min: 3, max: 255)]
    public ?string $name = null;

    #[Validator\Length(max: 1000)]
    public ?string $description = null;

    #[Validator\NotNull(message: 'Please upload a media file')]
    #[Validator\File(
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
    #[Validator\Count(
        min: 1,
        max: 20,
        minMessage: 'Please add at least one tag',
        maxMessage: 'You cannot add more than 20 tags',
    )]
    public array $tags = [];

    public function validated(): UploadContentCommand
    {
        Assert::notNull($this->name, 'Name is required');
        Assert::notNull($this->file, 'Please upload a media file');

        return new UploadContentCommand(
            name: $this->name,
            description: $this->description,
            file: new SymfonyUploadedFile($this->file),
            tags: $this->tags,
        );
    }

    /**
     * @return class-string<ContentType>
     */
    public function getType(): string
    {
        return ContentType::class;
    }
}
