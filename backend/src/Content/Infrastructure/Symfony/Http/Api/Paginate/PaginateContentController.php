<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Api\Paginate;

use olml89\TelegramUserbot\Backend\Content\Application\Paginate\PaginateContentCommandHandler;
use olml89\TelegramUserbot\Backend\Shared\Application\Pagination\PaginationException;
use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/content',
    name: 'api.content.paginate',
    defaults: ['_api' => true],
    methods: ['GET'],
)]
final readonly class PaginateContentController
{
    public function __construct(
        private PaginateContentCommandHandler $paginateContentCommandHandler,
    ) {}

    /**
     * @throws ValidationException
     * @throws PaginationException
     */
    public function __invoke(#[MapQueryString] PaginateContentQuery $query): JsonResponse
    {
        $paginateContentCommand = $query->command();
        $contentResults = $this->paginateContentCommandHandler->handle($paginateContentCommand);

        return new JsonResponse($contentResults, status: Response::HTTP_OK);
    }
}
