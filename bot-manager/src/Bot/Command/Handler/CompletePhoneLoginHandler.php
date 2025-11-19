<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\Handler;

use olml89\TelegramUserbot\BotManager\Bot\Command\Command;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\CompletePhoneLoginCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\HandlesCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\InvalidCommandException;
use olml89\TelegramUserbot\BotManager\Bot\Status\StatusManager;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\PhoneCodeStorage;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessManager;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStartedException;
use olml89\TelegramUserbot\Shared\Bot\Process\Process;
use olml89\TelegramUserbot\Shared\Bot\Status\InvalidStatusException;
use olml89\TelegramUserbot\Shared\Redis\RedisStorageException;

#[HandlesCommand(CompletePhoneLoginCommand::class)]
final readonly class CompletePhoneLoginHandler implements CommandHandler
{
    public function __construct(
        private StatusManager $statusManager,
        private PhoneCodeStorage $phoneCodeStorage,
        private ProcessManager $processManager,
    ) {
    }

    /**
     * @throws InvalidCommandException
     * @throws InvalidStatusException
     * @throws RedisStorageException
     * @throws ProcessNotStartedException
     */
    public function handle(Command $command): void
    {
        $command = CompletePhoneLoginCommand::validate($command, $this->statusManager->status());

        $this->phoneCodeStorage->store($command->phoneCode);
        $this->processManager->start(Process::CompletePhoneLogin);
    }
}
