<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper;

use DateTimeImmutable;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\Size\Size;
use olml89\TelegramUserbot\Backend\File\Domain\UnattachedFile;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\Event;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\IsEvent;

final readonly class FileMetadataStripped implements Event
{
    use IsEvent;

    public function __construct(
        private UnattachedFile $unattachedFile,
        private Size $oldSize,
        protected DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {}

    public function entity(): File
    {
        return $this->unattachedFile->file();
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $currentBytes = $this->entity()->bytes();
        $reducedBytes = $this->oldSize->diff($currentBytes);
        $percent = round(num: $reducedBytes / $currentBytes->value, precision: 2);

        return [
            'reducedBytes' => $reducedBytes,
            'reductionRatio' => $percent,
        ];
    }
}
