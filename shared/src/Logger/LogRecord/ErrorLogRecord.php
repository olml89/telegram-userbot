<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Logger\LogRecord;

use Psr\Log\LoggerInterface;
use Throwable;

final readonly class ErrorLogRecord extends LogRecord implements Loggable
{
    public Throwable $exception;

    public function __construct(string $message, Throwable $exception)
    {
        parent::__construct(message: $message);

        $this->exception = $exception;
    }

    /**
     * @return array<string, mixed>
     */
    protected function context(): array
    {
        return [
            'exception' => $this->exception,
        ];
    }

    public function log(LoggerInterface $logger): void
    {
        $logger->error($this->message, $this->context());
    }
}
