<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Api\RemoveFile;

use olml89\TelegramUserbot\Backend\Content\Application\RemoveFile\RemoveContentFileCommand;
use olml89\TelegramUserbot\Backend\Content\Application\RemoveFile\RemoveContentFileCommandHandler;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentNotFoundException;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentStorageException;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route(
    path: '/content/{contentId}/files/{fileId}',
    name: 'api.content.files.remove',
    defaults: ['_api' => true],
    methods: ['DELETE'],
)]
final readonly class RemoveContentFileController
{
    public function __construct(
        private RemoveContentFileCommandHandler $removeContentFileCommandHandler,
    ) {}

    /**
     * @throws ContentNotFoundException
     * @throws FileNotFoundException
     * @throws ContentStorageException
     */
    public function __invoke(Uuid $contentId, Uuid $fileId): JsonResponse
    {
        $removeContentFileCommand = new RemoveContentFileCommand($contentId, $fileId);
        $this->removeContentFileCommandHandler->handle($removeContentFileCommand);

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
