<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Status;

use olml89\TelegramUserbot\Shared\App\IsJsonSerializable;
use olml89\TelegramUserbot\Shared\App\IsStringable;
use Stringable;

/**
 * @mixin Status
 */
trait IsStatus
{
    use IsJsonSerializable;
    use IsStringable;

    public readonly StatusType $type;
    public readonly int $time;
    public readonly ?string $message;

    public function __construct(null|string|Stringable $message = null, ?int $time = null)
    {
        $this->type = $this->type();
        $this->time = $time ?? time();
        $this->message = is_null($message) ? $message : (string)$message;
    }

    abstract protected function type(): StatusType;

    public function withMessage(null|string|Stringable $message = null): Status
    {
        return new self($message, $this->time);
    }

    /**
     * @throws InvalidStatusException
     */
    public function assertEquals(StatusType ...$expectedStatusTypes): void
    {
        if (!in_array($this->type, $expectedStatusTypes, strict: true)) {
            throw new InvalidStatusException($this->type, ...$expectedStatusTypes);
        }
    }
}
