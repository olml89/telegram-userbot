<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application;

final readonly class FileCounter
{
    public function __construct(
        public int $images,
        public int $videos,
        public int $audios,
        public int $documents,
    ) {}
}
