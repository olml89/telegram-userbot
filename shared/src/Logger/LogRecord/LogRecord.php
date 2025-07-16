<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Logger\LogRecord;

abstract readonly class LogRecord
{
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return array<string, mixed>
     */
    abstract protected function context(): array;
}
