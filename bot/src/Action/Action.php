<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

use danog\MadelineProto\API;
use Throwable;

interface Action
{
    /**
     * @throws Throwable
     */
    public function run(API $api): void;
}
