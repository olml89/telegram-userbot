<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use ReflectionClass;
use RuntimeException;

final class CommandBus
{
    /**
     * @var array<class-string<Command>, CommandHandler>
     */
    private array $commandHandlers = [];

    /**
     * @param iterable<CommandHandler> $commandHandlers
     */
    public function __construct(iterable $commandHandlers)
    {
        foreach ($commandHandlers as $commandHandler) {
            $this->registerHandler($commandHandler);
        }
    }

    private function registerHandler(CommandHandler $commandHandler): void
    {
        $reflection = new ReflectionClass($commandHandler);
        $attributes = $reflection->getAttributes(HandlesCommand::class);

        if (count($attributes) === 0) {
            throw new RuntimeException(
                sprintf('CommandHandler %s must have a HandlesCommand attribute', $reflection->getName()),
            );
        }

        foreach ($attributes as $attribute) {
            /** @var HandlesCommand $instance */
            $instance = $attribute->newInstance();
            $commandClass = $instance->commandClass;

            if (array_key_exists($commandClass, $this->commandHandlers)) {
                throw new RuntimeException(
                    sprintf('Multiple handlers registered for command %s', $commandClass),
                );
            }

            $this->commandHandlers[$commandClass] = $commandHandler;
        }
    }

    public function dispatch(Command $command): void
    {
        $commandHandler = $this->commandHandlers[$command::class] ?? throw new RuntimeException(
            sprintf('No handler registered for Command %s', $command::class),
        );

        $commandHandler->handle($command);
    }
}
