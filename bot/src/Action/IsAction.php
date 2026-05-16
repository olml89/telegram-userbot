<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

/**
 * @mixin Action
 */
trait IsAction
{
    public function __toString(): string
    {
        $fqcn = $this::class;
        $className = basename(str_replace('\\', '/', $fqcn));

        $kebabCase = preg_replace(
            '/(?<!^)[A-Z]/',
            '-$0',
            $className,
        );

        return strtolower($kebabCase ?? $className);
    }
}
