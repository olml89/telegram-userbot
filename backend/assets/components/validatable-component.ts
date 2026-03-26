import { BackendErrorable, ChangeAware, ErrorClearable, Validatable } from './contracts';
import { HtmlLabelComponent } from './html-label-component';

export abstract class ValidatableComponent<TValue = unknown> extends HtmlLabelComponent<TValue> implements BackendErrorable, ChangeAware, ErrorClearable, Validatable {
    protected readonly changeListeners: Set<() => void> = new Set<() => void>();

    protected constructor(label: HTMLSpanElement) {
        super(label);

        this.onChange(() => void this.validate());
    }

    public clearErrors(): void {
        this.errorHandler.clearErrors();
        this.label.classList.remove('is-error');
        this.label.removeAttribute('title');
    }

    public override destroy(): void {
        this.changeListeners.clear();
    }

    protected abstract frontendErrors(): string[];
    protected abstract name(): string;

    public onChange(listener: () => void): void {
        this.changeListeners.add(listener);
    }

    public setBackendErrors(...errorMessages: string[]): void {
        this.errorHandler.setBackendErrors(this.getValue(), ...errorMessages);
    }

    public validate(): boolean {
        this.clearErrors();
        const frontendErrors = this.frontendErrors();

        if (frontendErrors.length > 0) {
            this.setErrors(...frontendErrors);

            return false;
        }

        const backendErrorMessagesFor = this.errorHandler.backendErrorMessagesFor(this.getValue());

        if (backendErrorMessagesFor.length > 0) {
            this.setErrors(...backendErrorMessagesFor);

            return false;
        }

        return true;
    }
}
