<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Infrastructure\Symfony\Http\Api\Store;

use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;
use olml89\TelegramUserbot\Backend\Tag\Application\Store\StoreTagCommandHandler;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagStorageException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/tags',
    name: 'api.tags.store',
    defaults: ['_api' => true],
    methods: ['POST'],
)]
final readonly class StoreTagController
{
    public function __construct(
        private StoreTagCommandHandler $storeTagCommandHandler,
    ) {}

    /**
     * @throws ValidationException
     * @throws TagStorageException
     */
    public function __invoke(#[MapRequestPayload] StoreTagRequest $request): JsonResponse
    {
        $storeTagCommand = $request->command();
        $storeTagResult = $this->storeTagCommandHandler->handle($storeTagCommand);
        $statusCode = $storeTagResult->created ? Response::HTTP_CREATED : Response::HTTP_OK;

        return new JsonResponse($storeTagResult, status: $statusCode);
    }
}
