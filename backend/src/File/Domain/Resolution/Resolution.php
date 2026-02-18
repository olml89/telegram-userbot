<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Resolution;

final readonly class Resolution
{
    /**
     * @throws ResolutionException
     */
    public function __construct(
        public int $width,
        public int $height,
    ) {
        if ($this->width <= 0) {
            throw ResolutionException::width();
        }

        if ($this->height <= 0) {
            throw ResolutionException::height();
        }
    }
}
