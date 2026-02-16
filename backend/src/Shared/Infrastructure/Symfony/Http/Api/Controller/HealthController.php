<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Http\Api\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/health',
    name: 'api.health',
    defaults: ['_api' => true],
    methods: ['GET'],
)]
final readonly class HealthController
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([
            'time' => time(),
        ]);
    }
}
