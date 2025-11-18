<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Process\LogRecord;

use olml89\TelegramUserbot\Shared\Bot\Process\ProcessType;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\InfoLogRecord;

final readonly class StartedProcess extends InfoLogRecord
{
    public ProcessType $processType;

    public function __construct(ProcessType $processType)
    {
        parent::__construct(message: 'Started process in bot container');

        $this->processType = $processType;
    }

    /**
     * @return array<string, mixed>
     */
    protected function context(): array
    {
        return [
            'process' => $this->processType->value,
        ];
    }
}
