<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Http\Api\Middleware;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST)]
final readonly class ForceTimeout
{
    public function __invoke(RequestEvent $event): void
    {
        if ($event->getRequest()->headers->has('X-Force-Timeout')) {
            $timeout = new Response(status: Response::HTTP_GATEWAY_TIMEOUT);
            $event->setResponse($timeout);
        }
    }
}
