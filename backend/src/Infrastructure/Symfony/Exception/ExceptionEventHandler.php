<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Exception;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;

interface ExceptionEventHandler
{
    public function handle(ExceptionEvent $event): void;
}
