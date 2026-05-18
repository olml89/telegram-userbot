import { BusyAware, ChangeAware, Component } from '../../components/contracts';
import { Pagination } from '../../models/pagination';
import { Counter } from './counter';
import { assertImported, querySelector } from '../../utils/importer';

export class PaginationComponent implements BusyAware, ChangeAware, Component<Pagination> {
    private readonly counter: Counter;
    private readonly paginationPanel: HTMLDivElement;
    private readonly pageSpan: HTMLSpanElement;
    private readonly previousPageBtn: HTMLButtonElement;
    private readonly nextPageBtn: HTMLButtonElement;
    private readonly changeListeners: Set<(pagination: Pagination) => void> = new Set();
    private pagination: Pagination = new Pagination(1, 10, 0);

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

        this.checkVisibility();

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

    private checkVisibility(): void {
        this.paginationPanel.classList.toggle('is-hidden', this.pagination.isEmpty());
        this.previousPageBtn.disabled = this.isFirstPage();
        this.nextPageBtn.disabled = this.pagination.isLastPage();
    }

    /**
     * On content delete
     *
     * 1. Decrease totalCount
     * 2. Update UI
     */
    public decreaseTotalCount(): void {
        this.update(this.pagination.decreaseTotalCount());
    }

    public firstPage(): void {
        this.update(this.pagination.firstPage());
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
        this.update(this.pagination.increaseTotalCount());
    }

    public isFirstPage(): boolean {
        return this.pagination.isFirstPage();
    }

    /**
     * On nextPageBtn click
     *
     * 1. Trigger change event with the next page
     * 2. The listener updates the UI after the fetch through this.update()
     */
    private next(): void {
        this.notifyChange(this.pagination.nextPage());
    }

    private notifyChange(pagination: Pagination): void {
        this.changeListeners.forEach((listener: (pagination: Pagination) => void): void => listener(pagination));
    }

    public onChange(listener: (pagination: Pagination) => void): void {
        this.changeListeners.add(listener);
    }

    /**
     * On previousPageBtn click
     *
     * 1. Trigger change event with the previous page
     * 2. The listener updates the UI after the fetch through this.update()
     */
    private previous(): void {
        this.notifyChange(this.pagination.previousPage());
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
     * 1. Trigger change event with the first page
     * 2. The listener updates the UI after the fetch through this.update()
     */
    public restart(): void {
        this.notifyChange(this.pagination.firstPage());
    }

    public setBusy(isBusy: boolean): void {
        this.previousPageBtn.disabled = isBusy || this.isFirstPage();
        this.nextPageBtn.disabled = isBusy || this.pagination.isLastPage();
    }

    /**
     * On fetch response
     *
     * 1. Update pagination from the outside
     * 2. Update UI
     */
    public update(pagination: Pagination): void {
        this.pagination = pagination;
        this.checkVisibility();
        this.pageSpan.textContent = this.pagination.formatPage();
        this.counter.update(this.pagination);
    }
}
