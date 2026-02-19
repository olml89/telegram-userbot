<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper;

use DateTimeImmutable;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\Size\Size;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\Event;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\IsEvent;

final readonly class FileMetadataStripped implements Event
{
    use IsEvent;

    public function __construct(
        private File $file,
        private Size $newSize,
        protected DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {}

    public function entity(): File
    {
        return $this->file;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $reducedBytes = $this->file->bytes()->diff($this->newSize);
        $percent = round($reducedBytes->value / $this->file->bytes()->value);

        return [
            'reducedBytes' => $reducedBytes->value,
            'reductionRatio' => $percent,
        ];
    }
}
