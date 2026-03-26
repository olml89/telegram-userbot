<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\Paginate;

use olml89\TelegramUserbot\Backend\Content\Application\ContentResult;
use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentQuery;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentRepository;
use olml89\TelegramUserbot\Backend\Shared\Application\Pagination\PaginationException;
use olml89\TelegramUserbot\Backend\Shared\Application\Pagination\PaginationResult;
use olml89\TelegramUserbot\Backend\Shared\Domain\Pagination\Pagination;

final readonly class ContentPaginator
{
    public function __construct(
        private int $perPage,
        private ContentRepository $contentRepository,
    ) {}

    /**
     * @return PaginationResult<ContentResult>
     *
     * @throws PaginationException
     */
    public function paginate(?int $page = null, ?ContentQuery $query = null): PaginationResult
    {
        $query ??= new ContentQuery();
        $pagination = new Pagination($this->perPage, $page ?? 1);
        $contents = $this->contentRepository->paginate($query, $pagination);

        return new PaginationResult(
            $pagination->page,
            $pagination->perPage,
            $contents->totalCount,
            ...$contents->map(fn(Content $content): ContentResult => ContentResult::content($content)),
        );
    }
}
