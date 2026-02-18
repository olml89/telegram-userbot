<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Entity;

use Symfony\Component\Uid\Uuid;

interface Entity
{
    public function id(): int;
    public function publicId(): Uuid;
}
