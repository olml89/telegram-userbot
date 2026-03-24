<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Api\Paginate;

use olml89\TelegramUserbot\Backend\Content\Application\Paginate\PaginateContentCommand;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Validation;

final readonly class PaginateContentQuery
{
    public function __construct(
        #[Validation\GreaterThanOrEqual(
            value: 1,
            message: 'The page must be at least 1',
        )]
        public ?int $page,

        public ?string $search,

        #[Validation\Uuid(message: 'The categoryId is invalid')]
        public ?string $categoryId,

        public ?string $mode,
    ) {}

    public function command(): PaginateContentCommand
    {
        $categoryId = is_null($this->categoryId) ? null : Uuid::fromString($this->categoryId);

        return new PaginateContentCommand(
            $this->page,
            $this->search,
            $categoryId,
            $this->mode,
        );
    }
}
