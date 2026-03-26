<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\Paginate;

use olml89\TelegramUserbot\Backend\Category\Domain\Category;
use olml89\TelegramUserbot\Backend\Category\Domain\CategoryFinder;
use olml89\TelegramUserbot\Backend\Category\Domain\CategoryNotFoundException;
use olml89\TelegramUserbot\Backend\Content\Application\ContentResult;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentQuery;
use olml89\TelegramUserbot\Backend\Content\Domain\Mode\Mode;
use olml89\TelegramUserbot\Backend\Content\Domain\Mode\UnsupportedModeException;
use olml89\TelegramUserbot\Backend\Shared\Application\Pagination\PaginationException;
use olml89\TelegramUserbot\Backend\Shared\Application\Pagination\PaginationResult;
use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;

final readonly class PaginateContentCommandHandler
{
    public function __construct(
        private CategoryFinder $categoryFinder,
        private ContentPaginator $contentPaginator,
    ) {}

    /**
     * @return PaginationResult<ContentResult>
     *
     * @throws ValidationException
     * @throws PaginationException
     */
    public function handle(PaginateContentCommand $command): PaginationResult
    {
        $validationException = new ValidationException();
        $category = $this->buildCategory($validationException, $command);
        $mode = $this->buildMode($validationException, $command);

        if ($validationException->hasErrors()) {
            throw $validationException;
        }

        $query = new ContentQuery(
            search: $command->search,
            category: $category,
            mode: $mode,
        );

        return $this->contentPaginator->paginate($command->page, $query);
    }

    private function buildCategory(ValidationException $validationException, PaginateContentCommand $command): ?Category
    {
        if (is_null($command->categoryId)) {
            return null;
        }

        try {
            return $this->categoryFinder->find($command->categoryId);
        } catch (CategoryNotFoundException $e) {
            $validationException->addError('categoryId', $e->getMessage());

            return null;
        }
    }

    private function buildMode(ValidationException $validationException, PaginateContentCommand $command): ?Mode
    {
        if (is_null($command->mode)) {
            return null;
        }

        try {
            return Mode::create($command->mode);
        } catch (UnsupportedModeException $e) {
            $validationException->addError('mode', $e->getMessage());

            return null;
        }
    }
}
