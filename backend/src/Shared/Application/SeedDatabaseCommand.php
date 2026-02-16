<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Application;

final readonly class SeedDatabaseCommand
{
    public function __construct(
        /** @var string[] */
        public array $categoryNames,
    ) {}
}
