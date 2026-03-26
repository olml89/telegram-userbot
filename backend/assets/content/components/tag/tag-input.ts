import { BusyAware, ErrorClearable, HtmlElementWrapper, Validatable } from '../../../components/contracts';
import { Tag } from '../../tag';
import { assertImported } from '../../../utils/importer';
import {BaseComponent} from "../../../components/base-component";

export class TagInput extends BaseComponent<string> implements BusyAware, ErrorClearable, HtmlElementWrapper, Validatable {
    private readonly input: HTMLInputElement;
    private readonly eventTarget: EventTarget = new EventTarget();

    private searchTimeout: number|undefined = undefined;
    private busy: boolean = false;

    public constructor(input: HTMLInputElement) {
        super();

        this.input = input;

        this.input.addEventListener('input', (): void => this.onInput());
        this.input.addEventListener('keydown', async(event: KeyboardEvent): Promise<void> => this.onKeydown(event));
        this.input.addEventListener('blur', (): void => this.onBlur());
        this.input.addEventListener('focus', (): void => this.onFocus());
    }

    public static from(inputElement: HTMLInputElement|null): TagInput|null {
        const required = {
            inputElement,
        };

        if (!assertImported('tag-input', required)) {
            return null;
        }

        return new TagInput(required.inputElement);
    }

    public clear(): void {
        this.input.value = '';
    }

    public clearErrors(): void {
        this.input.classList.remove('is-error');
        this.input.removeAttribute('title');
        this.input.setCustomValidity('');
    }

    public element(): HTMLInputElement {
        return this.input;
    }

    public frontendErrors(): string[] {
        let errors = [];

        if (this.input.maxLength > 0  && this.getValue().length > this.input.maxLength) {
            const tooLongMessage = `A tag cannot be longer than ${this.input.maxLength} characters`;
            errors.push(tooLongMessage);
        }

        return errors;
    }

    public override getValue(): string {
        return this.input.value.trim();
    }

    private onBlur(): void {
        if (this.busy) {
            return;
        }

        this.input.classList.remove('is-focused');
        this.eventTarget.dispatchEvent(new CustomEvent('tag-input:blur'));
    }

    public onBlurEvent(listener: () => void): void {
        this.eventTarget.addEventListener('tag-input:blur', (): void => listener());
    }

    public onEnter(listener: (value: string) => void): void {
        this.eventTarget.addEventListener('tag-input:enter', (event: Event): void => {
            listener((event as CustomEvent<string>).detail);
        });
    }

    private onFocus(): void {
        if (this.busy) {
            this.input.blur();

            return;
        }

        this.input.classList.add('is-focused');
        this.eventTarget.dispatchEvent(new CustomEvent('tag-input:focus'));
    }

    public onFocusEvent(listener: () => void): void {
        this.eventTarget.addEventListener('tag-input:focus', (): void => listener());
    }

    private onInput(): void {
        if (this.busy) {
            return;
        }

        const value = this.getValue();

        if (value.length === 0) {
            this.eventTarget.dispatchEvent(new CustomEvent('tag-input:query:clear'));

            return;
        }

        if (!this.validate()) {
            return;
        }

        this.clearErrors();
        clearTimeout(this.searchTimeout);

        this.searchTimeout = setTimeout(() => {
            this.eventTarget.dispatchEvent(new CustomEvent('tag-input:query', { detail: value }));
        }, 400);
    }

    private async onKeydown(event: KeyboardEvent): Promise<void> {
        if (event.isComposing) {
            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            this.eventTarget.dispatchEvent(new CustomEvent('tag-input:navigate', { detail: 'next' }));

            return;
        }

        if (event.key === 'ArrowUp') {
            event.preventDefault();
            this.eventTarget.dispatchEvent(new CustomEvent('tag-input:navigate', { detail: 'previous' }));

            return;
        }

        const isEnter = event.key === 'Enter';

        if (!isEnter) {
            return;
        }

        event.preventDefault();
        this.eventTarget.dispatchEvent(new CustomEvent('tag-input:enter', { detail: this.getValue() }));
    }

    public onNavigate(listener: (direction: 'next'|'previous') => void): void {
        this.eventTarget.addEventListener('tag-input:navigate', (event: Event): void => {
            listener((event as CustomEvent<'next'|'previous'>).detail);
        });
    }

    public onQuery(listener: (query: string) => void): void {
        this.eventTarget.addEventListener('tag-input:query', (event: Event): void => {
            listener((event as CustomEvent<string>).detail);
        });
    }

    public onQueryClear(listener: () => void): void {
        this.eventTarget.addEventListener('tag-input:query:clear', (): void => listener());
    }

    public onSelectedTag(listener: (tag: Tag) => void): void {
        this.eventTarget.addEventListener('tag-input:selected', (event: Event): void => {
            listener((event as CustomEvent<Tag>).detail);
        });
    }

    public refocus(): void {
        this.input.classList.add('is-focused');
        this.input.focus();
        this.input.setSelectionRange(this.getValue().length, this.getValue().length);
    }

    public setBusy(isBusy: boolean): void {
        this.busy = isBusy;
        this.input.disabled = isBusy;

        if (this.busy) {
            this.input.blur();
            this.input.classList.remove('is-focused');

            return;
        }

        this.refocus();
    }

    public override setErrors(...errorMessages: string[]): void {
        const errorMessage = errorMessages.join(' · ');
        this.input.classList.add('is-error');
        this.input.setAttribute('title', errorMessage);
    }

    public validate(): boolean {
        const frontendErrors = this.frontendErrors();

        if (frontendErrors.length > 0) {
            this.setErrors(...frontendErrors);

            return false;
        }

        return true;
    }
}
