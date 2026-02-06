<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\Store;

use olml89\TelegramUserbot\Backend\Content\Domain\Language;
use olml89\TelegramUserbot\Backend\Content\Domain\Mode;
use olml89\TelegramUserbot\Backend\Content\Domain\Status;
use Symfony\Component\Uid\Uuid;

final readonly class StoreContentCommand
{
    public function __construct(
        public string $title,
        public string $description,
        public int $intensity,
        public float $price,
        public Language $language,
        public Mode $mode,
        public Status $status,
        public Uuid $categoryId,

        /** @var Uuid[] */
        public array $tagIds,

        /** @var Uuid[] */
        public array $fileIds,
    ) {
    }
}
