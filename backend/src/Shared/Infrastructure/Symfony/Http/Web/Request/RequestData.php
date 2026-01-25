<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Http\Web\Request;

use Symfony\Component\Form\AbstractType;

interface RequestData
{
    /**
     * @return class-string<AbstractType>
     */
    public function getType(): string;
}
