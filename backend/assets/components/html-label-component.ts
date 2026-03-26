import { ErrorAware } from './contracts';
import { BaseComponent } from './base-component';

export abstract class HtmlLabelComponent<TValue = unknown> extends BaseComponent<TValue> implements ErrorAware {
    protected readonly label: HTMLSpanElement;

    protected constructor(label: HTMLSpanElement) {
        super();

        this.label = label;
    }

    public hasErrors(): boolean {
        return this.errorHandler.hasErrors();
    }

    public override setErrors(...errorMessages: string[]): void {
        super.setErrors(...errorMessages);

        const errorMessage = errorMessages.join(' · ');
        this.label.classList.add('is-error');
        this.label.setAttribute('title', errorMessage);
    }
}
