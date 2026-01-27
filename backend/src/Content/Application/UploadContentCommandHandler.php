<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application;

use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentFileManager;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentRepository;
use olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile\UploadedFileException;
use olml89\TelegramUserbot\Backend\Shared\Domain\ExceptionHandler;
use Symfony\Component\Uid\Uuid;
use Throwable;

final readonly class UploadContentCommandHandler
{
    public function __construct(
        private ContentFileManager $contentFileManager,
        private ExceptionHandler $exceptionHandler,
        private ContentRepository $contentRepository,
    ) {
    }

    public function handle(UploadContentCommand $command): ?Content
    {
        try {
            $file = $this->contentFileManager->save($command->file);
        } catch (UploadedFileException $e) {
            $this->exceptionHandler->handle($e);

            return null;
        }

        try {
            $content = new Content(
                publicId: Uuid::v4(),
                name: $command->name,
                description: $command->description,
                file: $file,
                tags: $command->tags,
            );

            $this->contentRepository->store($content);

            return $content;
        } catch (Throwable $e) {
            // Rollback: delete the created content file
            $this->contentFileManager->remove($file);
            $this->exceptionHandler->handle($e);

            return null;
        }
    }
}
