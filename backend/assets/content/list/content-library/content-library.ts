import { ChangeAware, Component } from '../../../common/component/contracts';
import { SearchInput } from './search-input';
import { PaginationComponent } from './pagination-component';
import { ContentList } from './content-list';
import { CategorySelect } from '../../category-select';
import { ModeSelect } from '../../mode-select';
import { Tag } from '../../tag';
import { Content } from '../../content';
import { ContentAddModal } from '../../add/add-modal';
import { Paginated } from '../../../common/models/pagination';
import { BackendError } from '../../../common/backend-error';
import { assertImported, querySelector } from '../../../common/importer';

export class ContentQueryFields implements ChangeAware, Component<string> {
    public readonly searchInput: SearchInput;
    private readonly category: CategorySelect;
    private readonly mode: ModeSelect;
    public readonly pagination: PaginationComponent;
    private readonly changeListeners: Set<(isFilterChange: boolean) => void> = new Set();

    public constructor(
        searchInput: SearchInput,
        category: CategorySelect,
        mode: ModeSelect,
        pagination: PaginationComponent,
    ) {
        this.searchInput = searchInput;
        this.category = category;
        this.mode = mode;
        this.pagination = pagination;
    }

    public static from(
        libraryFilters: HTMLDivElement|null,
        libraryCounter: HTMLDivElement|null,
        libraryPagination: HTMLDivElement|null,
    ): ContentQueryFields|null {
        const searchInput = SearchInput.from(querySelector<HTMLInputElement>(libraryFilters, '[data-content-search]'));
        const category = CategorySelect.from(querySelector<HTMLLabelElement>(libraryFilters, '[data-library-category]'));
        const mode = ModeSelect.from(querySelector<HTMLLabelElement>(libraryFilters, '[data-library-mode]'));
        const pagination = PaginationComponent.from(libraryCounter, libraryPagination);

        const required = {
            searchInput,
            category,
            mode,
            pagination,
        };

        if (!assertImported('content-query-fields', required)) {
            return null;
        }

        return new ContentQueryFields(
            required.searchInput,
            required.category,
            required.mode,
            required.pagination,
        );
    }

    public getValue(): string {
        const search = this.searchInput.getValue();
        const categoryId = this.category.getValue()?.publicId;
        const mode = this.mode.getValue()?.value;
        const page = this.pagination.getValue().page;
        const params = new URLSearchParams();

        if (search) {
            params.set('search', search);
        }

        if (categoryId) {
            params.set('categoryId', categoryId);
        }

        if (mode) {
            params.set('mode', mode);
        }

        params.set('page', page.toString());
        const query = params.toString();

        return query ? `?${query}` : '';
    }

    public matches(content: Content): boolean {
        const category = this.category.getValue();

        if (category && !category.equals(content.category)) {
            return false;
        }

        const mode = this.mode.getValue();

        if (mode && mode.equals(content.mode)) {
            return false;
        }

        const searchTerm = this.searchInput.getValue()?.toLowerCase();

        if (searchTerm) {
            return content.title.toLowerCase().includes(searchTerm)
                || content.description.toLowerCase().includes(searchTerm)
                || content.tags.some((tag: Tag) => tag.name.toLowerCase().includes(searchTerm));
        }

        return true;
    }

    public onChange(listener: (isFilterChange: boolean) => void): void {
        this.changeListeners.add(listener);

        /**
         * Register filters listeners
         */
        const filterListener = (): void => this.changeListeners.forEach(
            (listener: (isFilterChange: boolean) => void): void => listener(true),
        );

        this.searchInput.onChange(filterListener);
        this.category.onChange(filterListener);
        this.mode.onChange(filterListener);

        /**
         * Register pagination listeners
         */
        const paginationListener = (): void => this.changeListeners.forEach(
            (listener: (isFilterChange: boolean) => void): void => listener(false),
        );

        this.pagination.onChange(paginationListener);
    }
}

export class ContentLibrary {
    private readonly contentQueryFields: ContentQueryFields;
    private readonly contentList: ContentList;
    private readonly openContentAddModalBtn: HTMLButtonElement;
    private readonly contentAddModal: ContentAddModal;
    private isLoading: boolean = false;

    public constructor(
        contentQueryFields: ContentQueryFields,
        contentList: ContentList,
        openContentAddModalBtn: HTMLButtonElement,
        contentAddModal: ContentAddModal,
    ) {
        this.contentQueryFields = contentQueryFields;
        this.contentList = contentList
        this.openContentAddModalBtn = openContentAddModalBtn;
        this.contentAddModal = contentAddModal;

        this.openContentAddModalBtn.addEventListener('click', (): void => this.contentAddModal.open());
        this.contentAddModal.onAddedContent((content: Content): void => this.contentList.add(content, this.contentQueryFields));
        this.contentQueryFields.onChange(async (isFilterChange: boolean): Promise<void> => this.load(isFilterChange));
    }

    public static create(): ContentLibrary|null {
        const contentList = ContentList.from(
            querySelector<HTMLDivElement>(document, '[data-library-notifications]'),
            querySelector<HTMLTableElement>(document, '[data-library-table-body]'),
        );

        const contentQueryFields = ContentQueryFields.from(
            querySelector<HTMLDivElement>(document, '[data-library-filters]'),
            querySelector<HTMLDivElement>(document, '[data-library-counter]'),
            querySelector<HTMLDivElement>(document, '[data-library-pagination]'),
        );

        const openContentAddModalBtn = querySelector<HTMLButtonElement>(document, '[data-content-open]');
        const contentAddModal = ContentAddModal.from(document.getElementById('contentAddModal') as HTMLDivElement|null);

        const required = {
            contentList,
            contentQueryFields,
            openContentAddModalBtn,
            contentAddModal,
        };

        if (!assertImported('contents-component', required)) {
            return null;
        }

        return new ContentLibrary(
            required.contentQueryFields,
            required.contentList,
            required.openContentAddModalBtn,
            required.contentAddModal,
        )
    }

    private async fetch(query: string): Promise<Paginated<Content>> {
        const response = await fetch(`/api/content${query}`);

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to fetch content list',
            );
        }

        const payload = await response.json();

        return Paginated.from<Content>(payload, Content);
    }

    private async load(isFilterChange: boolean): Promise<void> {
        if (this.isLoading) {
            return;
        }

        if (isFilterChange) {
            this.contentQueryFields.pagination.restart();
        }

        this.isLoading = true;

        try {
            const query = this.contentQueryFields.getValue();
            const paginatedContents = await this.fetch(query);
            this.contentQueryFields.pagination.update(paginatedContents.pagination);

            this.contentList.replace(
                paginatedContents.list,
                this.contentQueryFields.searchInput.getValue(),
            );
        } catch (e: any) {
            const backendError = e as BackendError;
            console.log(backendError);
            this.contentList.error(backendError);
            this.contentQueryFields.pagination.reset();
        } finally {
            this.isLoading = false;
        }
    }
}
