import { InputComponent } from './input-component';
import { assertImported, querySelector } from '../../utils/importer';

export class TextInput extends InputComponent<string> {
    public static from(textInputContainer: HTMLLabelElement|null): TextInput|null {
        const label = querySelector<HTMLSpanElement>(textInputContainer, '[data-error-for]');
        const input = querySelector<HTMLInputElement>(textInputContainer, '[data-text-input]');

        const required = {
            textInputContainer,
            label,
            input,
        }

        if (!assertImported('text-input', required)) {
            return null;
        }

        return new TextInput(
            required.label,
            required.input,
        );
    }

    protected override frontendErrors(): string[] {
        let errors = super.frontendErrors();

        if (errors.length > 0) {
            return errors;
        }

        if (this.minLength() > 0 && this.getValue().length < this.minLength()) {
            const tooShortMessage = `${this.name()} must be at least ${this.minLength()} characters`;
            errors.push(tooShortMessage);
        }

        if (this.maxLength() > 0 && this.getValue().length > this.maxLength()) {
            const tooLongMessage = `${this.name()} cannot be longer than ${this.maxLength()} characters`;
            errors.push(tooLongMessage);
        }

        return errors;
    }

    public override getValue(): string {
        return this.input.value.trim();
    }

    protected override isEmpty(): boolean {
        return this.getValue().length === 0;
    }
    
    private maxLength(): number {
        return this.input.maxLength;
    }
    
    private minLength(): number {
        return this.input.minLength;
    }
}
