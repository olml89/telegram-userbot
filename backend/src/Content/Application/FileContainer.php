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

final readonly class FileContainer implements Result
{
    use IsResult;

    public function __construct(
        public FileCounter $count,

        /**
        * @var array<ImageResult|VideoResult|AudioResult|FileResult>
        */
        public array $list,
    ) {}

    /**
     * @param Collection<File> $files
     */
    public static function files(Collection $files): self
    {
        $fileResults = [];
        $imageCount = 0;
        $audioCount = 0;
        $videoCount = 0;
        $documentCount = 0;

        $files->each(
            function (File $file) use (&$fileResults, &$imageCount, &$audioCount, &$videoCount, &$documentCount): void {
                $fileResult = FileResultFactory::create($file);
                $fileResults[] = $fileResult;

                match (true) {
                    $fileResult instanceof ImageResult => ++$imageCount,
                    $fileResult instanceof AudioResult => ++$audioCount,
                    $fileResult instanceof VideoResult => ++$videoCount,
                    $fileResult instanceof FileResult => ++$documentCount,
                };
            },
        );

        return new self(
            count: new FileCounter(
                images: $imageCount,
                videos: $videoCount,
                audios: $audioCount,
                documents: $documentCount,
            ),
            list: $fileResults,
        );
    }
}
