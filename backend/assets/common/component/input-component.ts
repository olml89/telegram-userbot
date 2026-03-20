import { BusyAware } from './contracts';
import { RequirableComponent } from './requirable-component';
import { capitalize } from '../strings';

export abstract class InputComponent<TValue = unknown> extends RequirableComponent<TValue> implements BusyAware {
    protected readonly input: HTMLInputElement;

    public constructor(label: HTMLSpanElement, input: HTMLInputElement) {
        super(label, input.required);

        this.input = input;

        this.input.addEventListener('input', (): void => {
            this.changeListeners.forEach((listener: () => void): void => listener());
        });
    }

    public override clearErrors(): void {
        super.clearErrors();

        this.input.classList.remove('is-error');
        this.input.setCustomValidity('');
    }

    public override destroy() {
        super.destroy();

        this.input.value = '';
    }

    protected disabled(): boolean {
        return this.input.disabled;
    }

    protected override name(): string {
        return this.input.name;
    }

    public setBusy(isBusy: boolean): void {
        this.label.classList.toggle('is-disabled', isBusy);

        this.input.disabled = isBusy;
        this.input.setAttribute('aria-disabled', String(isBusy));
        this.input.disabled = isBusy;
    }

    protected override requiredMessage(): string {
        return `${capitalize(this.name())} is required`;
    }

    public override setErrors(...errorMessages: string[]) {
        super.setErrors(...errorMessages);

        this.input.classList.add('is-error');
        this.input.setCustomValidity(this.label.title);
    }
}
