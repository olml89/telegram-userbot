<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action\LogRecord;

use olml89\TelegramUserbot\Bot\Action\Action;
use olml89\TelegramUserbot\BotRuntime\Logger\LogRecord\InfoLogRecord;

abstract readonly class ActionLogRecord extends InfoLogRecord
{
    public Action $action;

    public function __construct(string $message, Action $action)
    {
        parent::__construct($message);

        $this->action = $action;
    }

    protected function context(): array
    {
        return [
            'action' => (string) $this->action,
        ];
    }
}
