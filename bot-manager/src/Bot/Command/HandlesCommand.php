<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class HandlesCommand
{
    /**
     * @var class-string<Command>
     */
    public string $commandClass;

    public function __construct(string $class)
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(
                sprintf('Command class %s does not exist', $class),
            );
        }

        if (!is_subclass_of($class, Command::class)) {
            throw new InvalidArgumentException(
                sprintf('Command class %s must extend %s', $class, Command::class),
            );
        }

        $this->commandClass = $class;
    }
}
