import { ChangeAware, Component } from '../common/component/contracts';
import { ContentAddModal } from './add-modal';
import { ContentNotifications, ContentsList } from './contents-list';
import { CategorySelect } from './category/category-select';
import { ModeSelect } from './mode/mode-select';
import { Content } from './content';
import { BackendError } from '../common/backend-error';
import { Paginated, Pagination } from '../common/models/pagination';
import { assertImported, querySelector } from '../common/importer';

class SearchBox<TValue = string|null> implements ChangeAware, Component<TValue> {
    protected readonly input: HTMLInputElement;
    protected readonly changeListeners: Set<() => void> = new Set<() => void>();
    private searchTimeout: number|undefined = undefined;

    public constructor(input: HTMLInputElement) {
        this.input = input;

        this.input.addEventListener('input', (): void => {
            clearTimeout(this.searchTimeout);

            this.searchTimeout = setTimeout(() => {
                this.changeListeners.forEach((listener: () => void): void => listener());
            }, 400);
        });
    }

    public static from(input: HTMLInputElement|null): SearchBox|null {
        const required = {
            input,
        };

        if (!assertImported('content-search-box', required)) {
            return null;
        }

        return new SearchBox(required.input);
    }

    public getValue(): string|null {
        const search = this.input.value.trim();

        if (!search.length) {
            return null;
        }

        return search;
    }

    public onChange(listener: () => void): void {
        this.changeListeners.add(listener);
    }
}

class Counter implements Component<number> {
    private readonly emptyCounterSpan: HTMLSpanElement;
    private readonly countSpan: HTMLSpanElement;
    private count: number = 0;

    public constructor(emptyCounterSpan: HTMLSpanElement, countSpan: HTMLSpanElement) {
        this.emptyCounterSpan = emptyCounterSpan;
        this.countSpan = countSpan;
    }

    public static from(libraryCounter: HTMLDivElement|null): Counter|null {
        const emptyCounterSpan = querySelector<HTMLSpanElement>(libraryCounter, '[data-library-counter-empty]');
        const countSpan = querySelector<HTMLSpanElement>(libraryCounter, '[data-library-counter-count]');

        const required = {
            libraryCounter,
            emptyCounterSpan,
            countSpan,
        };

        if (!assertImported('content-counter', required)) {
            return null;
        }

        return new Counter(required.emptyCounterSpan, required.countSpan);
    }

    public getValue(): number {
        return this.count;
    }

    public update(pagination: Pagination): void {
        this.count = pagination.totalCount;
        this.countSpan.textContent = pagination.formatCount();
        this.countSpan.hidden = pagination.isEmpty();
        this.emptyCounterSpan.hidden = !pagination.isEmpty();
    }
}

/**
 * Declared on: templates/content/list.html.twig
 */
declare const paginationData: {
    page: number;
    perPage: number;
    totalCount: number;
    pageCount: number;
};

class PaginationControls implements ChangeAware, Component<Pagination> {
    private readonly counter: Counter;
    private readonly paginationPanel: HTMLDivElement;
    private readonly pageSpan: HTMLSpanElement;
    private readonly previousPageBtn: HTMLButtonElement;
    private readonly nextPageBtn: HTMLButtonElement;
    private readonly changeListeners: Set<() => void> = new Set<() => void>();
    private pagination: Pagination;

    public constructor(
        counter: Counter,
        paginationPanel: HTMLDivElement,
        pageSpan: HTMLSpanElement,
        previousPageBtn: HTMLButtonElement,
        nextPageBtn: HTMLButtonElement,
    ) {
        this.counter = counter;
        this.paginationPanel = paginationPanel;
        this.pageSpan = pageSpan;
        this.previousPageBtn = previousPageBtn;
        this.nextPageBtn = nextPageBtn;

        /**
         * Declared on: templates/content/list.html.twig
         */
        this.pagination = Pagination.fromJson(paginationData);
        this.update(this.pagination);

        this.previousPageBtn.addEventListener('click', (): void => this.modifyPage(-1));
        this.nextPageBtn.addEventListener('click', (): void => this.modifyPage(1));
    }

    public static from(libraryCounter: HTMLDivElement|null, paginationPanel: HTMLDivElement|null): PaginationControls|null {
        const counter = Counter.from(libraryCounter);
        const pageSpan = querySelector<HTMLDivElement>(paginationPanel, '[data-library-pagination-page]');
        const previousPageBtn = querySelector<HTMLButtonElement>(paginationPanel, '[data-library-pagination-previous]');
        const nextPageBtn = querySelector<HTMLButtonElement>(paginationPanel, '[data-library-pagination-next]');

        const required = {
            counter,
            paginationPanel,
            pageSpan,
            previousPageBtn,
            nextPageBtn,
        };

        if (!assertImported('content-pagination-controls', required)) {
            return null;
        }

        return new PaginationControls(
            required.counter,
            required.paginationPanel,
            required.pageSpan,
            required.previousPageBtn,
            required.nextPageBtn,
        );
    }

    private checkButtonState(): void {
        this.previousPageBtn.disabled = !this.pagination.hasPreviousPage();
        this.nextPageBtn.disabled = !this.pagination.hasNextPage();
    }

    public getValue(): Pagination {
        return this.pagination;
    }

    private modifyPage(direction: 1|-1): void {
        this.pagination = this.pagination.modifyPage(direction);
        this.changeListeners.forEach((listener: () => void): void => listener());
    }

    public onChange(listener: () => void): void {
        this.changeListeners.add(listener);
    }

    private checkVisibility(): void {
        this.paginationPanel.classList.toggle('is-hidden', this.pagination.isEmpty());
    }

    public reset(): void {
        this.update(this.pagination.reset());
    }

    public restart(): void {
        this.update(this.pagination.restart());
    }

    public update(pagination: Pagination): void {
        this.pagination = pagination;
        this.checkButtonState();
        this.checkVisibility();
        this.pageSpan.textContent = pagination.formatPage();
        this.counter.update(pagination);
    }
}

class ContentQueryFields implements ChangeAware, Component<string> {
    private readonly searchBox: SearchBox;
    private readonly category: CategorySelect;
    private readonly mode: ModeSelect;
    public readonly pagination: PaginationControls;
    private readonly changeListeners: Set<(isFilterChange: boolean) => void> = new Set();

    public constructor(
        searchBox: SearchBox,
        category: CategorySelect,
        mode: ModeSelect,
        pagination: PaginationControls,
    ) {
        this.searchBox = searchBox;
        this.category = category;
        this.mode = mode;
        this.pagination = pagination;
    }

    public static from(
        libraryFilters: HTMLDivElement|null,
        libraryCounter: HTMLDivElement|null,
        libraryPagination: HTMLDivElement|null,
    ): ContentQueryFields|null {
        const searchBox = SearchBox.from(querySelector<HTMLInputElement>(libraryFilters, '[data-content-search]'));
        const category = CategorySelect.from(querySelector<HTMLLabelElement>(libraryFilters, '[data-library-category]'));
        const mode = ModeSelect.from(querySelector<HTMLLabelElement>(libraryFilters, '[data-library-mode]'));
        const pagination = PaginationControls.from(libraryCounter, libraryPagination);

        const required = {
            searchBox,
            category,
            mode,
            pagination,
        };

        if (!assertImported('content-query-fields', required)) {
            return null;
        }

        return new ContentQueryFields(
            required.searchBox,
            required.category,
            required.mode,
            required.pagination,
        );
    }

    public getValue(): string {
        const search = this.searchBox.getValue();
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

    public onChange(listener: (isFilterChange: boolean) => void): void {
        this.changeListeners.add(listener);

        /**
         * Register filters listeners
         */
        const filterListener = (): void => this.changeListeners.forEach(
            (listener: (isFilterChange: boolean) => void): void => listener(true),
        );

        this.searchBox.onChange(filterListener);
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

export class ContentsComponent  {
    private readonly contentQueryFields: ContentQueryFields;
    private readonly contentsList: ContentsList;
    private readonly openContentAddModalBtn: HTMLButtonElement;
    private readonly contentAddModal: ContentAddModal;
    private isLoading: boolean = false;

    public constructor(
        contentQueryFields: ContentQueryFields,
        contents: ContentsList,
        openContentAddModalBtn: HTMLButtonElement,
        contentAddModal: ContentAddModal,
    ) {
        this.contentQueryFields = contentQueryFields;
        this.contentsList = contents
        this.openContentAddModalBtn = openContentAddModalBtn;
        this.contentAddModal = contentAddModal;

        this.contentQueryFields.onChange(async (isFilterChange: boolean): Promise<void> => {
            if (this.isLoading) {
                return;
            }

            if (isFilterChange) {
                this.contentQueryFields.pagination.restart();
            }

            this.isLoading = true;

            try {
                const paginatedContents = await this.fetch();
                this.contentsList.show(paginatedContents.list);
                this.contentQueryFields.pagination.update(paginatedContents.pagination);
            } catch (e: any) {
                const backendError = e as BackendError;
                console.log(backendError);
                this.contentsList.error(backendError);
                this.contentQueryFields.pagination.reset();
            } finally {
                this.isLoading = false;
            }
        });

        this.openContentAddModalBtn.addEventListener('click', (): void => this.contentAddModal.open());
        this.contentAddModal.onAddedContent((content: Content): void => this.contentsList.add(content));
    }

    public static create(): ContentsComponent|null {
        const contents = ContentsList.from(
            ContentNotifications.from(querySelector<HTMLDivElement>(document, '[data-library-notifications]')),
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
            contents,
            contentQueryFields,
            openContentAddModalBtn,
            contentAddModal,
        };

        if (!assertImported('contents-component', required)) {
            return null;
        }

        return new ContentsComponent(
            required.contentQueryFields,
            required.contents,
            required.openContentAddModalBtn,
            required.contentAddModal,
        )
    }

    private async fetch(): Promise<Paginated<Content>> {
        const response = await fetch(`/api/content${this.contentQueryFields.getValue()}`);

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to fetch content list',
            );
        }

        const payload = await response.json();

        return Paginated.from<Content>(payload);
    }
}
