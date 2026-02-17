<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\Store;

use Symfony\Component\Uid\Uuid;

final readonly class StoreContentCommand
{
    public function __construct(
        public string $title,
        public string $description,
        public int $intensity,
        public float $price,
        public string $language,
        public string $mode,
        public string $status,
        public Uuid $categoryId,

        /** @var Uuid[] */
        public array $tagIds,

        /** @var Uuid[] */
        public array $fileIds,
    ) {}
}
