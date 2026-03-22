<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application;

use olml89\TelegramUserbot\Backend\File\Domain\Audio;

final readonly class AudioResult extends FileResult
{
    public float $duration;

    public function __construct(Audio $audio)
    {
        parent::__construct($audio);

        $this->duration = $audio->duration()->value;
    }
}
