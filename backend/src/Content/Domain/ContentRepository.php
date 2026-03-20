<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\Collection;

interface ContentRepository
{
    /** @return Collection<Content> */
    public function all(): Collection;

    public function getByTitle(string $title): ?Content;
    public function store(Content $content): void;
}
