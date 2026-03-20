<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application;

use olml89\TelegramUserbot\Backend\File\Application\AudioResult;
use olml89\TelegramUserbot\Backend\File\Application\FileResult;
use olml89\TelegramUserbot\Backend\File\Application\FileResultFactory;
use olml89\TelegramUserbot\Backend\File\Application\ImageResult;
use olml89\TelegramUserbot\Backend\File\Application\VideoResult;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\IsResult;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\Result;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\Collection;

final readonly class FilesResult implements Result
{
    use IsResult;

    public function __construct(
        public int $imageCount,
        public int $audioCount,
        public int $videoCount,
        public int $documentCount,

        /**
        * @var FileResult[]
        */
        public array $list,
    ) {
    }

    public static function files(Collection $files): self
    {
        $fileResults =  $files->map(
            fn(File $file): ImageResult|AudioResult|VideoResult|FileResult => FileResultFactory::create($file),
        );

        $imageCount = 0;
        $audioCount = 0;
        $videoCount = 0;
        $documentCount = 0;

        $fileResults->each(
            fn (ImageResult|AudioResult|VideoResult|FileResult $result) => match (true) {
                $result instanceof ImageResult => ++$imageCount,
                $result instanceof AudioResult => ++$audioCount,
                $result instanceof VideoResult => ++$videoCount,
                $result instanceof FileResult => ++$documentCount,
            }
        );

        return new self(
            imageCount: $imageCount,
            audioCount: $audioCount,
            videoCount: $videoCount,
            documentCount: $documentCount,
            list: $fileResults->toArray(),
        );
    }
}
