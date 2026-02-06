<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Web\List;

use olml89\TelegramUserbot\Backend\Content\Application\List\ListContentCommandHandler;
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
    ) {
    }

    public function __invoke(): Response
    {
        $listContentResult = $this->listContentCommandHandler->handle();

        return $this->render('pages/library/list.html.twig', [
            'active_menu' => 'library.list',
            'categories' => $listContentResult->categories,
            'modes' => $listContentResult->modes,
            'languages' => $listContentResult->languages,
            'statuses' => $listContentResult->statuses,
        ]);
    }
}
