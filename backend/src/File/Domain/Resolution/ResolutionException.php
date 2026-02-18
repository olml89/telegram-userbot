<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Resolution;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

final class ResolutionException extends InvariantException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function width(): self
    {
        return new self('The width of the frame has to be greater than 0');
    }

    public static function height(): self
    {
        return new self('The height of the frame has to be greater than 0');
    }
}
