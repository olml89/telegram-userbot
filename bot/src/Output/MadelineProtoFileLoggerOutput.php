<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Output;

use RuntimeException;
use Stringable;

final readonly class MadelineProtoFileLoggerOutput implements Output
{
    /**
     * @var string[]
     */
    private const array UNDESIRED_LOG_CHANNELS = [
        'APIWrapper',
        'PingLoop',
        'Session',
        'SessionPaths',
    ];

    private string $line;

    public function __construct(string|Stringable $line)
    {
        $this->line = match (true) {
            is_string($line) => trim($line),
            $line instanceof Stringable => trim((string) $line),
        };
    }

    public function isBroadcastable(): bool
    {
        if (mb_strlen($this->line) === 0) {
            return false;
        }

        return !array_any(
            self::UNDESIRED_LOG_CHANNELS,
            fn(string $channel): bool => $this->isFromChannel($channel),
        );
    }

    private function isFromChannel(string $channel): bool
    {
        return str_contains($this->line, sprintf('%s:', $channel));
    }

    public function __toString(): string
    {
        if (!$this->isBroadcastable()) {
            throw new RuntimeException(
                sprintf('Output not broadcastable: %s', $this->line),
            );
        }

        if (!str_contains($this->line, "\t") || substr_count($this->line, needle: "\t") > 1) {
            return $this->line;
        }

        [, $lineWithoutChannel] = explode("\t", $this->line);

        return $lineWithoutChannel;
    }
}
