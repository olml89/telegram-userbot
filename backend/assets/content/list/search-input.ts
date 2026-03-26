import { BusyAware, ChangeAware, Component } from '../../components/contracts';
import { assertImported } from '../../utils/importer';

export class SearchInput implements BusyAware, ChangeAware, Component<string|null> {
    protected readonly input: HTMLInputElement;
    protected readonly changeListeners: Set<() => void> = new Set<() => void>();
    private searchTimeout: number|undefined = undefined;
    private shouldRestoreFocus: boolean = false;

    public constructor(input: HTMLInputElement) {
        this.input = input;

        this.input.addEventListener('input', (): void => {
            clearTimeout(this.searchTimeout);

            this.searchTimeout = setTimeout(() => {
                this.changeListeners.forEach((listener: () => void): void => listener());
            }, 400);
        });
    }

    public static from(input: HTMLInputElement|null): SearchInput|null {
        const required = {
            input,
        };

        if (!assertImported('content-search-box', required)) {
            return null;
        }

        return new SearchInput(required.input);
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

    public setBusy(isBusy: boolean): void {
        /**
         * When busy, remember if whe should restore focus to the input when is not busy any more
         */
        if (isBusy) {
            this.shouldRestoreFocus = document.activeElement === this.input;
        }

        this.input.disabled = isBusy;

        /**
         * When not busy, restore focus to the input if needed
         */
        if (!isBusy && this.shouldRestoreFocus) {
            requestAnimationFrame((): void => {
                this.input.focus();
                this.shouldRestoreFocus = false;
            });
        }
    }
}
