<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Web\List;

use olml89\TelegramUserbot\Backend\Content\Application\List\ListContentCommandHandler;
use olml89\TelegramUserbot\Backend\Shared\Application\Pagination\PaginationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/library',
    name: 'library.list',
    methods: ['GET'],
)]
final class ListContentController extends AbstractController
{
    public function __construct(
        private readonly ListContentCommandHandler $listContentCommandHandler,
    ) {}

    /**
     * @throws PaginationException
     */
    public function __invoke(): Response
    {
        $listContentResult = $this->listContentCommandHandler->handle();

        return $this->render('content/list.html.twig', [
            'active_menu' => 'library.list',
            'contents' => $listContentResult->contents,
            'categories' => $listContentResult->categories,
            'modes' => $listContentResult->modes,
            'languages' => $listContentResult->languages,
            'statuses' => $listContentResult->statuses,
        ]);
    }
}
