<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\Command;

use olml89\TelegramUserbot\BotManager\Bot\Command\BaseProcessableCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandType;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\PhoneCode;
use olml89\TelegramUserbot\Shared\Bot\Process\Process;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStartedException;
use olml89\TelegramUserbot\Shared\Bot\Status\InvalidStatusException;
use olml89\TelegramUserbot\Shared\Redis\RedisStorageException;

final readonly class CompletePhoneLoginCommand extends BaseProcessableCommand implements Command
{
    public PhoneCode $phoneCode;

    public function __construct(PhoneCode $phoneCode)
    {
        parent::__construct(CommandType::CompletePhoneLogin, Process::CompletePhoneLogin);

        $this->phoneCode = $phoneCode;
    }

    /**
     * @throws InvalidStatusException
     * @throws RedisStorageException
     * @throws ProcessNotStartedException
     */
    public function handle(CommandHandler $commandHandler): void
    {
        $commandHandler->completePhoneLogin($this);
    }
}
