<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Api\Store;

use olml89\TelegramUserbot\Backend\Content\Application\Store\StoreContentCommand;
use olml89\TelegramUserbot\Backend\Content\Domain\Language;
use olml89\TelegramUserbot\Backend\Content\Domain\Mode;
use olml89\TelegramUserbot\Backend\Content\Domain\Status;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Validation;
use Webmozart\Assert\Assert;

final readonly class StoreContentRequest
{
    public function __construct(

        #[Validation\NotNull(message: 'The title is required')]
        public ?string $title,

        #[Validation\NotNull(message: 'The description is required')]
        public ?string $description,

        #[Validation\NotNull(message: 'The intensity is required')]
        public ?int $intensity,

        #[Validation\NotNull(message: 'The price is required')]
        public ?float $price,

        #[Validation\NotNull(message: 'The language is required')]
        #[Validation\Choice(
            callback: [Language::class, 'values'],
            message: 'The language is invalid',
        )]
        public ?string $language,

        #[Validation\NotNull(message: 'The mode is required')]
        #[Validation\Choice(
            callback: [Mode::class, 'values'],
            message: 'The mode is invalid',
        )]
        public ?string $mode,

        #[Validation\NotNull(message: 'The status is required')]
        #[Validation\Choice(
            callback: [Status::class, 'values'],
            message: 'The status is invalid',
        )]
        public ?string $status,

        #[Validation\NotBlank(message: 'The categoryId is required')]
        #[Validation\Uuid(message: 'The categoryId is invalid')]
        public ?string $categoryId,

        /** @var string[] */
        #[Validation\All(
            constraints: [
                new Validation\Uuid(message: 'Each tagId must be a valid UUID'),
            ],
        )]
        public ?array $tagIds,

        /** @var string[] */
        #[Validation\All(
            constraints: [
                new Validation\Uuid(message: 'Each fileId must be a valid UUID'),
            ],
        )]
        public ?array $fileIds,
    ) {}

    public function command(): StoreContentCommand
    {
        Assert::notNull($this->title);
        Assert::notNull($this->description);
        Assert::notNull($this->intensity);
        Assert::notNull($this->price);

        Assert::notNull($this->language);
        $language = Language::from($this->language);

        Assert::notNull($this->mode);
        $mode = Mode::from($this->mode);

        Assert::notNull($this->status);
        $status = Status::from($this->status);

        Assert::notNull($this->categoryId);
        $categoryId = Uuid::fromString($this->categoryId);

        Assert::notNull($this->tagIds);
        $tagIds = array_map(
            fn(string $tagId): Uuid => Uuid::fromString($tagId),
            $this->tagIds,
        );

        Assert::notNull($this->fileIds);
        $fileIds = array_map(
            fn(string $fileId): Uuid => Uuid::fromString($fileId),
            $this->fileIds,
        );

        return new StoreContentCommand(
            title: $this->title,
            description: $this->description,
            intensity: $this->intensity,
            price: $this->price,
            language: $language,
            mode: $mode,
            status: $status,
            categoryId: $categoryId,
            tagIds: $tagIds,
            fileIds: $fileIds,
        );
    }
}
