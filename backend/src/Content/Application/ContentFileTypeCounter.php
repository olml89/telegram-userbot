<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application;

use JsonSerializable;
use olml89\TelegramUserbot\Application\IsJsonSerializable;
use olml89\TelegramUserbot\Backend\File\Application\AudioResult;
use olml89\TelegramUserbot\Backend\File\Application\FileResult;
use olml89\TelegramUserbot\Backend\File\Application\ImageResult;
use olml89\TelegramUserbot\Backend\File\Application\VideoResult;

final class ContentFileTypeCounter implements JsonSerializable
{
    use IsJsonSerializable;

    private int $images = 0;
    private int $videos = 0;
    private int $audios = 0;
    private int $documents = 0;

    public function __construct(FileResult ...$fileResults)
    {
        foreach ($fileResults as $fileResult) {
            $this->add($fileResult);
        }
    }

    private function add(FileResult $fileResult): self
    {
        match (true) {
            $fileResult instanceof ImageResult => ++$this->images,
            $fileResult instanceof AudioResult => ++$this->audios,
            $fileResult instanceof VideoResult => ++$this->videos,
            $fileResult instanceof FileResult => ++$this->documents,
        };

        return $this;
    }
}
