<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Output;

use Stringable;

final readonly class MadelineProtoCallableLoggerOutput implements Output
{
    private string $output;

    public function __construct(string|Stringable $output)
    {
        $this->output = is_string($output) ? $output : (string) $output;
    }

    public function isBroadcastable(): bool
    {
        return true;
    }

    public function __toString(): string
    {
        return $this->output;
    }
}
