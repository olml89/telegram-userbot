<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Http\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/',
    name: 'dashboard',
    methods: ['GET'],
)]
final class DashboardController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('pages/dashboard.html.twig', [
            'active_menu' => 'dashboard',
        ]);
    }
}
