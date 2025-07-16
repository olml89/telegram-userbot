<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Process\LogRecord;

use olml89\TelegramUserbot\Shared\Bot\Process\Process;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\InfoLogRecord;

final readonly class StoppedProcess extends InfoLogRecord
{
    public Process $process;

    public function __construct(Process $process)
    {
        parent::__construct(message: 'Stopped process in bot container');

        $this->process = $process;
    }

    /**
     * @return array<string, mixed>
     */
    protected function context(): array
    {
        return [
            'process' => $this->process,
        ];
    }
}
