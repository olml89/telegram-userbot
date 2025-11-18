<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot;

use olml89\TelegramUserbot\BotManager\Bot\Command\Command\CompletePhoneLoginCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\LogoutCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\PhoneLoginCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\RequestStatusCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\StartCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\StopCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandHandler;
use olml89\TelegramUserbot\BotManager\Bot\Status\StatusManager;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\PhoneCodeStorage;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessManager;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStartedException;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStoppedException;
use olml89\TelegramUserbot\Shared\Bot\Status\InvalidStatusException;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use olml89\TelegramUserbot\Shared\Redis\RedisStorageException;
use Throwable;

/**
 * Checks if a Command is executable based on the current Status from the bot.
 *
 * If it is, it executes it on the bot container through supervisorctl.
 */
final readonly class BotRunner implements CommandHandler
{
    public function __construct(
        private StatusManager $statusManager,
        private ProcessManager $processManager,
        private PhoneCodeStorage $phoneCodeStorage,
        private LoggableLogger $loggableLogger,
    ) {
    }

    public function broadcastStatus(?Throwable $e = null): void
    {
        $this->statusManager->emit($e);
    }

    /**
     * @throws ProcessNotStartedException
     */
    public function requestStatus(RequestStatusCommand $command): void
    {
        $command->process()->start($this->processManager, $this->loggableLogger);
    }

    /**
     * @throws InvalidStatusException
     * @throws ProcessNotStartedException
     */
    public function phoneLogin(PhoneLoginCommand $command): void
    {
        $command->checkAllowedBy($this->statusManager->status());

        $command->process()->start($this->processManager, $this->loggableLogger);
    }

    /**
     * @throws InvalidStatusException
     * @throws RedisStorageException
     * @throws ProcessNotStartedException
     */
    public function completePhoneLogin(CompletePhoneLoginCommand $command): void
    {
        $command->checkAllowedBy($this->statusManager->status());

        $this->phoneCodeStorage->store($command->phoneCode());
        $command->process()->start($this->processManager, $this->loggableLogger);
    }

    /**
     * @throws InvalidStatusException
     * @throws ProcessNotStartedException
     */
    public function logout(LogoutCommand $command): void
    {
        $command->checkAllowedBy($this->statusManager->status());

        $command->process()->start($this->processManager, $this->loggableLogger);
    }

    /**
     * @throws InvalidStatusException
     * @throws ProcessNotStartedException
     */
    public function start(StartCommand $command): void
    {
        $command->checkAllowedBy($this->statusManager->status());

        $command->process()->start($this->processManager, $this->loggableLogger);
    }

    /**
     * @throws InvalidStatusException
     * @throws ProcessNotStoppedException
     */
    public function stop(StopCommand $command): void
    {
        $command->checkAllowedBy($this->statusManager->status());

        $command->process()->stop($this->processManager, $this->loggableLogger);
    }
}
