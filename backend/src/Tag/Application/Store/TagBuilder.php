<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Application\Store;

use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name\Name;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name\NameLengthException;
use olml89\TelegramUserbot\Backend\Tag\Domain\Tag;
use Symfony\Component\Uid\Uuid;

final readonly class TagBuilder
{
    /**
     * @throws ValidationException
     */
    public function build(StoreTagCommand $command): Tag
    {
        $validationException = new ValidationException();
        $name = $this->buildName($validationException, $command);

        if ($validationException->hasErrors()) {
            throw $validationException;
        }

        /**
         * @var Name $name
         */
        return new Tag(
            publicId: Uuid::v4(),
            name: $name,
        );
    }

    private function buildName(ValidationException $validationException, StoreTagCommand $command): ?Name
    {
        try {
            return new Name($command->name);
        } catch (NameLengthException $e) {
            $validationException->addError('name', $e->getMessage());

            return null;
        }
    }
}
