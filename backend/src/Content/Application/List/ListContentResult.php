<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\List;

use olml89\TelegramUserbot\Backend\Category\Application\CategoryResult;
use olml89\TelegramUserbot\Backend\Content\Domain\Language;
use olml89\TelegramUserbot\Backend\Content\Domain\Mode;
use olml89\TelegramUserbot\Backend\Content\Domain\Status;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\IsResult;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\Result;

final readonly class ListContentResult implements Result
{
    use IsResult;

    public function __construct(
        /** @var CategoryResult[] */
        public array $categories,

        /** @var Mode[] */
        public array $modes,

        /** @var Status[] */
        public array $statuses,

        /** @var Language[] */
        public array $languages,
    ) {}
}
