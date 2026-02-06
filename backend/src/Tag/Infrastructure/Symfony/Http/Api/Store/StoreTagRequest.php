<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Infrastructure\Symfony\Http\Api\Store;

use olml89\TelegramUserbot\Backend\Tag\Application\Store\StoreTagCommand;
use Symfony\Component\Validator\Constraints as Validation;
use Webmozart\Assert\Assert;

final readonly class StoreTagRequest
{
    public function __construct(
        #[Validation\NotBlank]
        #[Validation\Length(
            max: 50,
            maxMessage: 'The tag cannot be longer than 50 characters',
        )]
        public ?string $name,
    ) {
    }

    public function command(): StoreTagCommand
    {
        Assert::notNull($this->name);

        return new StoreTagCommand($this->name);
    }
}
