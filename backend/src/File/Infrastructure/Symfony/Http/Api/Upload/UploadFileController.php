<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Http\Api\Upload;

use olml89\TelegramUserbot\Backend\File\Application\Upload\UploadFileCommandHandler;
use olml89\TelegramUserbot\Backend\File\Domain\FileStorageException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumptionException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadNotFoundException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadReadingException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadRemovalException;
use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\UnsupportedResourceException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/files',
    name: 'api.files.upload',
    defaults: ['_api' => true],
    methods: ['POST'],
)]
final readonly class UploadFileController
{
    public function __construct(
        private UploadFileCommandHandler $uploadFileCommandHandler,
    ) {}

    /**
     * @throws UploadNotFoundException
     * @throws UploadReadingException
     * @throws UnsupportedResourceException
     * @throws ValidationException
     * @throws UploadConsumptionException
     * @throws UploadRemovalException
     * @throws FileStorageException
     */
    public function __invoke(#[MapRequestPayload] UploadFileRequest $request): JsonResponse
    {
        $uploadFileCommand = $request->command();
        $uploadFileResult = $this->uploadFileCommandHandler->handle($uploadFileCommand);

        return new JsonResponse($uploadFileResult, status: Response::HTTP_CREATED);
    }
}
