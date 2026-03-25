import {BusyAware, ChangeAware, Component} from '../../../common/component/contracts';
import { Pagination } from '../../../common/models/pagination';
import { Counter } from './counter';
import { assertImported, querySelector } from '../../../common/importer';

/**
 * Declared on: templates/content/list.html.twig
 */
declare const paginationData: {
    page: number;
    perPage: number;
    totalCount: number;
    pageCount: number;
};

export class PaginationComponent implements BusyAware, ChangeAware, Component<Pagination> {
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

        this.previousPageBtn.addEventListener('click', (): void => this.previous());
        this.nextPageBtn.addEventListener('click', (): void => this.next());
    }

    public static from(libraryCounter: HTMLDivElement|null, paginationPanel: HTMLDivElement|null): PaginationComponent|null {
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

        if (!assertImported('content-pagination-component', required)) {
            return null;
        }

        return new PaginationComponent(
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

    private checkVisibility(): void {
        this.paginationPanel.classList.toggle('is-hidden', this.pagination.isEmpty());
    }

    public getValue(): Pagination {
        return this.pagination;
    }

    /**
     * On optimistic update
     *
     * 1. Set firstPage, increase totalCount
     * 2. Update UI
     */
    public increaseTotalCount(): void {
        this.update(this.pagination.firstPage().increaseTotalCount());
    }

    public isFirstPage(): boolean {
        return this.pagination.isFirstPage();
    }

    /**
     * On nextPageBtn click
     *
     * 1. Set nextPage
     * 2. Update UI
     * 3. Trigger change event
     */
    private next(): void {
        this.update(this.pagination.nextPage());
        this.notifyChange();
    }

    private notifyChange(): void {
        this.changeListeners.forEach((listener: () => void): void => listener());
    }

    public onChange(listener: () => void): void {
        this.changeListeners.add(listener);
    }

    /**
     * On previousPageBtn click
     *
     * 1. Set previousPage
     * 2. Update UI
     * 3. Trigger change event
     */
    public previous(): void {
        this.pagination = this.pagination.previousPage();
        this.notifyChange();
    }

    /**
     * On fetching error
     *
     * 1. Set firstPage, reset totalCount (0)
     * 2. Update UI
     */
    public reset(): void {
        this.update(this.pagination.firstPage().resetTotalCount());
    }

    /**
     * On filter change event
     *
     * 1. Set firstPage
     * 2. Trigger change event
     */
    public restart(): void {
        this.pagination = this.pagination.firstPage();
        this.notifyChange();
    }

    public setBusy(isBusy: boolean): void {
        this.previousPageBtn.disabled = isBusy;
        this.nextPageBtn.disabled = isBusy;
    }

    /**
     * On fetch response
     *
     * 1. Update pagination from the outside
     * 2. Update UI
     */
    public update(pagination: Pagination): void {
        this.pagination = pagination;
        this.checkButtonState();
        this.checkVisibility();
        this.pageSpan.textContent = pagination.formatPage();
        this.counter.update(pagination);
    }
}
