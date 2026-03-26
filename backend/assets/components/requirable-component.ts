import { ValidatableComponent } from './validatable-component';

export abstract class RequirableComponent<TValue = unknown> extends ValidatableComponent<TValue> {
    protected readonly required: boolean;

    protected constructor(label: HTMLSpanElement, required: boolean) {
        super(label);

        this.required = required;
    }

    protected override frontendErrors(): string[] {
        let errors = [];

        if (this.required && this.isEmpty()) {
            errors.push(this.requiredMessage());
        }

        return errors;
    }

    protected abstract isEmpty(): boolean;
    protected abstract requiredMessage(): string;
}
