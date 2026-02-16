<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action\Action;

use olml89\TelegramUserbot\Bot\Action\Action;
use olml89\TelegramUserbot\Bot\MadelineProto\ApiWrapper;

final readonly class RequestStatus implements Action
{
    public function run(ApiWrapper $apiWrapper): void {}
}
