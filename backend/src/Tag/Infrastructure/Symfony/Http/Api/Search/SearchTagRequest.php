<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Infrastructure\Symfony\Http\Api\Search;

use olml89\TelegramUserbot\Backend\Tag\Application\Search\SearchTagCommand;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchTagRequest
{
    public function __construct(
        #[Assert\NotBlank(allowNull: true)]
        public ?string $query,
    ) {}

    public function command(): SearchTagCommand
    {
        return new SearchTagCommand($this->query);
    }
}
