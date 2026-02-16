<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Http\Api\Remove;

use olml89\TelegramUserbot\Backend\File\Application\Remove\RemoveFileCommand;
use olml89\TelegramUserbot\Backend\File\Application\Remove\RemoveFileCommandHandler;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotFoundException;
use olml89\TelegramUserbot\Backend\File\Domain\FileStorageException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route(
    path: '/files/{publicId}',
    name: 'api.files.delete',
    defaults: ['_api' => true],
    methods: ['DELETE'],
)]
final readonly class RemoveFileController
{
    public function __construct(
        private RemoveFileCommandHandler $removeFileCommandHandler,
    ) {}

    /**
     * @throws FileNotFoundException
     * @throws FileStorageException
     */
    public function __invoke(Uuid $publicId): JsonResponse
    {
        $removeFileCommand = new RemoveFileCommand($publicId);
        $this->removeFileCommandHandler->handle($removeFileCommand);

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
