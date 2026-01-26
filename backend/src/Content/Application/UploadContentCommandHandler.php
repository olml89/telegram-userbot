<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application;

use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentRepository;
use olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile\UploadedFileSaver;
use Symfony\Component\Uid\Uuid;

final readonly class UploadContentCommandHandler
{
    public function __construct(
        private UploadedFileSaver $uploadedFileSaver,
        private ContentRepository $contentRepository,
    ) {
    }

    public function handle(UploadContentCommand $command): void
    {
        $file = $this->uploadedFileSaver->save($command->file);

        $content = new Content(
            publicId: Uuid::v4(),
            name: $command->name,
            description: $command->description,
            file: $file,
            tags: $command->tags,
        );

        $this->contentRepository->store($content);
    }
}
