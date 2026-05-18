<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action\LogRecord;

use olml89\TelegramUserbot\Bot\Action\Action;

final readonly class ActionFinished extends ActionLogRecord
{
    public function __construct(Action $action)
    {
        parent::__construct('Action finished', $action);
    }
}
