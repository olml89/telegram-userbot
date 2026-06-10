<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application;

use olml89\TelegramUserbot\Application\IsJsonSerializable;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentFile\ContentFile;
use olml89\TelegramUserbot\Backend\File\Application\FileResult;
use olml89\TelegramUserbot\Backend\Shared\Application\Result;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\ReadonlyCollection;

final readonly class ContentFileContainer implements Result
{
    use IsJsonSerializable;

    public function __construct(
        public ContentFileTypeCounter $types,

        /**
        * @var FileResult[]
        */
        public array $list,
    ) {}

    /**
     * @param ReadonlyCollection<int, ContentFile> $contentFiles
     */
    public static function files(ReadonlyCollection $contentFiles): self
    {
        /** @var FileResult[] $fileResults */
        $fileResults = $contentFiles
            ->map(fn(ContentFile $contentFile): FileResult => FileResult::file($contentFile->file()))
            ->values()
            ->toArray();

        return new self(
            types: new ContentFileTypeCounter(...$fileResults),
            list: $fileResults,
        );
    }
}
