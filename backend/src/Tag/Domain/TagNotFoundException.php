<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\NotFoundException;
use Symfony\Component\Uid\Uuid;

final class TagNotFoundException extends NotFoundException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function publicId(Uuid $publicId): self
    {
        return new self(
            sprintf(
                'Tag %s not found',
                $publicId->toRfc4122(),
            ),
        );
    }

    public static function name(string $name): self
    {
        return new self(
            sprintf(
                'Tag with name %s not found',
                $name,
            ),
        );
    }
}
