import { Component } from '../../components/contracts';
import { assertImported, querySelector } from '../../utils/importer';
import { Pagination } from '../../models/pagination';

export class Counter implements Component<number> {
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
