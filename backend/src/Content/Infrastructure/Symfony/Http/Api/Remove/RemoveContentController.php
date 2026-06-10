<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Api\Remove;

use olml89\TelegramUserbot\Backend\Content\Application\Remove\RemoveContentCommand;
use olml89\TelegramUserbot\Backend\Content\Application\Remove\RemoveContentCommandHandler;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentNotFoundException;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentStorageException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route(
    path: '/contents/{publicId}',
    name: 'api.content.remove',
    defaults: ['_api' => true],
    methods: ['DELETE'],
)]
final readonly class RemoveContentController
{
    public function __construct(
        private RemoveContentCommandHandler $removeContentCommandHandler,
    ) {}

    /**
     * @throws ContentNotFoundException
     * @throws ContentStorageException
     */
    public function __invoke(Uuid $publicId): JsonResponse
    {
        $removeContentCommand = new RemoveContentCommand($publicId);
        $this->removeContentCommandHandler->handle($removeContentCommand);

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
