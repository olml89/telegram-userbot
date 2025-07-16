<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action\LogRecord;

use olml89\TelegramUserbot\Bot\Action\Action;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\InfoLogRecord;

final readonly class ActionStarted extends InfoLogRecord
{
    public Action $action;

    public function __construct(Action $action)
    {
        parent::__construct(message: 'Action started');

        $this->action = $action;
    }

    protected function context(): array
    {
        return [
            'action' => $this->action::class,
        ];
    }
}
