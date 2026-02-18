<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Status;

use JsonSerializable;
use olml89\TelegramUserbot\Shared\App\IsJsonSerializable;
use olml89\TelegramUserbot\Shared\App\IsStringable;
use Stringable;

final readonly class Status implements JsonSerializable, Stringable
{
    use IsJsonSerializable;
    use IsStringable;

    public StatusType $type;
    private ?string $message;
    private int $time;

    public function __construct(StatusType $type, string|Stringable|null $message = null, ?int $time = null)
    {
        $this->type = $type;
        $this->message = is_null($message) ? $message : (string) $message;
        $this->time = $time ?? time();
    }

    public function withMessage(string|Stringable|null $message): Status
    {
        return new self($this->type, $message, $this->time);
    }
}
