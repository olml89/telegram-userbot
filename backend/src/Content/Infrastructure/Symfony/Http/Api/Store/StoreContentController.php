<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Api\Store;

use olml89\TelegramUserbot\Backend\Category\Domain\CategoryNotFoundException;
use olml89\TelegramUserbot\Backend\Content\Application\Store\StoreContentCommandHandler;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentStorageException;
use olml89\TelegramUserbot\Backend\File\Domain\FileAlreadyAttachedException;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotFoundException;
use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/content',
    name: 'api.content.store',
    defaults: ['_api' => true],
    methods: ['POST'],
)]
final readonly class StoreContentController
{
    public function __construct(
        private StoreContentCommandHandler $storeContentCommandHandler,
    ) {}

    /**
     * @throws CategoryNotFoundException
     * @throws TagNotFoundException
     * @throws FileNotFoundException
     * @throws FileAlreadyAttachedException
     * @throws ValidationException
     * @throws ContentStorageException
     */
    public function __invoke(#[MapRequestPayload] StoreContentRequest $request): JsonResponse
    {
        $storeContentCommand = $request->command();
        $storeContentResult = $this->storeContentCommandHandler->handle($storeContentCommand);

        return new JsonResponse($storeContentResult, status: Response::HTTP_CREATED);
    }
}
