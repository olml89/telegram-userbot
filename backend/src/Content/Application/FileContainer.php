<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application;

use olml89\TelegramUserbot\Backend\File\Application\AudioResult;
use olml89\TelegramUserbot\Backend\File\Application\FileResult;
use olml89\TelegramUserbot\Backend\File\Application\ImageResult;
use olml89\TelegramUserbot\Backend\File\Application\VideoResult;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\IsResult;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\Result;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\Collection;

final readonly class FileContainer implements Result
{
    use IsResult;

    public function __construct(
        public FileCounter $count,

        /**
        * @var FileResult[]
        */
        public array $list,
    ) {}

    /**
     * @param Collection<File> $files
     */
    public static function files(Collection $files): self
    {
        $imageCount = 0;
        $audioCount = 0;
        $videoCount = 0;
        $documentCount = 0;

        $fileResults = $files->map(
            function (File $file) use (&$imageCount, &$audioCount, &$videoCount, &$documentCount): FileResult {
                $fileResult = FileResult::file($file);

                match (true) {
                    $fileResult instanceof ImageResult => ++$imageCount,
                    $fileResult instanceof AudioResult => ++$audioCount,
                    $fileResult instanceof VideoResult => ++$videoCount,
                    $fileResult instanceof FileResult => ++$documentCount,
                };

                return $fileResult;
            },
        );

        return new self(
            count: new FileCounter(
                images: $imageCount,
                videos: $videoCount,
                audios: $audioCount,
                documents: $documentCount,
            ),
            list: $fileResults->toArray(),
        );
    }
}
