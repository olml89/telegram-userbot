<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action\Action;

use danog\MadelineProto\API;
use olml89\TelegramUserbot\Bot\Action\Action;

final readonly class RequestStatus implements Action
{
    public function run(API $api): void
    {
    }
}
