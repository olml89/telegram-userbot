<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Http\Web\Request;

use olml89\TelegramUserbot\Backend\Shared\Application\Command;
use Symfony\Component\Form\AbstractType;

/**
 * @template TCommand of Command
 */
interface FormData
{
    /** @return TCommand */
    public function validated(): Command;

    /**
     * @return class-string<AbstractType>
     */
    public function getType(): string;
}
