<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Http\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DisplayUserbotController extends AbstractController
{
    #[Route('/userbot', name: 'userbot', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('userbot.html.twig', [
            'active_menu' => 'userbot',
        ]);
    }
}
