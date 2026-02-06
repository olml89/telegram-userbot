<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Infrastructure\Symfony\Http\Api\Store;

use olml89\TelegramUserbot\Backend\Tag\Application\Store\StoreTagCommand;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class StoreTagRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(
            max: 50,
            maxMessage: 'The tag cannot be longer than 50 characters',
        )]
        public string $name,
    ) {
    }

    public function command(): StoreTagCommand
    {
        return new StoreTagCommand($this->name);
    }
}
