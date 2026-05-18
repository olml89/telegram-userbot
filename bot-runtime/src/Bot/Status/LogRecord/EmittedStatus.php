<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotRuntime\Bot\Status\LogRecord;

use olml89\TelegramUserbot\BotRuntime\Bot\Status\Status;
use olml89\TelegramUserbot\BotRuntime\Logger\LogRecord\InfoLogRecord;

final readonly class EmittedStatus extends InfoLogRecord
{
    public Status $status;

    public function __construct(Status $status)
    {
        parent::__construct('Emitted status');

        $this->status = $status;
    }

    /**
     * @return array<string, mixed>
     */
    protected function context(): array
    {
        return [
            'status' => $this->status,
        ];
    }
}
