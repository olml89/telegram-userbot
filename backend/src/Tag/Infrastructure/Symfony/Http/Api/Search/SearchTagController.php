<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Infrastructure\Symfony\Http\Api\Search;

use olml89\TelegramUserbot\Backend\Tag\Application\Search\SearchTagCommandHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/tags',
    name: 'api.tags.search',
    defaults: ['_api' => true],
    methods: ['GET'],
)]
final readonly class SearchTagController
{
    public function __construct(
        private SearchTagCommandHandler $searchTagCommandHandler,
    ) {}

    public function __invoke(#[MapQueryString] SearchTagRequest $request): JsonResponse
    {
        $searchTagCommand = $request->command();
        $searchTagResult = $this->searchTagCommandHandler->handle($searchTagCommand);

        return new JsonResponse($searchTagResult, status: Response::HTTP_OK);
    }
}
