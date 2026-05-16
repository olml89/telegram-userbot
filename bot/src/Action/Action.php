<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

use olml89\TelegramUserbot\Bot\MadelineProto\ApiWrapper;
use Stringable;
use Throwable;

interface Action extends Stringable
{
    /**
     * @throws Throwable
     */
    public function run(ApiWrapper $apiWrapper): void;
}
