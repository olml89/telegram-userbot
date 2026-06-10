<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\List;

use olml89\TelegramUserbot\Application\IsJsonSerializable;
use olml89\TelegramUserbot\Backend\Category\Application\CategoryResult;
use olml89\TelegramUserbot\Backend\Content\Domain\Language\Language;
use olml89\TelegramUserbot\Backend\Content\Domain\Mode\Mode;
use olml89\TelegramUserbot\Backend\Content\Domain\Status\Status;
use olml89\TelegramUserbot\Backend\Shared\Application\Result;

final readonly class ListContentResult implements Result
{
    use IsJsonSerializable;

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
