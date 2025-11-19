<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\Command;

use olml89\TelegramUserbot\BotManager\Bot\Command\CommandType;
use olml89\TelegramUserbot\BotManager\Bot\Command\IsStatusRestrictedCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\StatusRestrictedCommand;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\PhoneCode;
use olml89\TelegramUserbot\Shared\Bot\Status\StatusType;

final readonly class CompletePhoneLoginCommand implements StatusRestrictedCommand
{
    use IsStatusRestrictedCommand;

    public PhoneCode $phoneCode;

    public function __construct(PhoneCode $phoneCode)
    {
        $this->type = CommandType::CompletePhoneLogin;
        $this->phoneCode = $phoneCode;
    }

    /**
     * @return StatusType[]
     */
    protected static function allowedStatusTypes(): array
    {
        return [
            StatusType::WaitingCode,
        ];
    }
}
