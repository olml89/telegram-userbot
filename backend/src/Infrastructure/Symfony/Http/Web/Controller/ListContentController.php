<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Http\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ListContentController extends AbstractController
{
    #[Route('/content', name: 'content', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('content.html.twig', [
            'active_menu' => 'content',
        ]);
    }
}
