<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action\Action;

use olml89\TelegramUserbot\Bot\Action\Action;
use olml89\TelegramUserbot\Bot\MadelineProto\ApiWrapper;
use olml89\TelegramUserbot\Bot\MadelineProto\BotEventHandler;

final readonly class Loop implements Action
{
    public function run(ApiWrapper $apiWrapper): void
    {
        $apiWrapper->startLoop(eventHandlerClass: BotEventHandler::class);
    }
}
