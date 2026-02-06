<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

interface ContentRepository
{
    public function getByTitle(string $title): ?Content;
    public function store(Content $content): void;
}
