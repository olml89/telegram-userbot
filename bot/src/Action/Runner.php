<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

use danog\MadelineProto\API;
use olml89\TelegramUserbot\Bot\MadelineProto\BotEventHandler;

final readonly class Runner implements Action
{
    public function run(API $api): void
    {
        API::startAndLoopMulti([$api], BotEventHandler::class);
    }
}
