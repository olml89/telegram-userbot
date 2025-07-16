<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Status\LogRecord;

use olml89\TelegramUserbot\Shared\Bot\Status\Status;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\InfoLogRecord;

final readonly class ReceivedStatus extends InfoLogRecord
{
    public Status $status;

    public function __construct(Status $status)
    {
        parent::__construct('Received status');

        $this->status = $status;
    }

    protected function context(): array
    {
        return [
            'status' => $this->status,
        ];
    }
}
