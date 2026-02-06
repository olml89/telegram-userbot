<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\NotFoundException;

final class ContentNotFoundException extends NotFoundException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function title(string $title): self
    {
        return new self(
            sprintf(
                'Content with title %s not found',
                $title,
            ),
        );
    }
}
