<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use Symfony\Component\Uid\Uuid;

final class Content
{
    public function __construct(
        public readonly Uuid $publicId,
        private string $name,
        private ?string $description,
        private File $file,
        /** @var string[] */
        private array $tags,
        private ?int $id = null,
    ) {
    }
}
