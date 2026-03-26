import { NumberInput } from '../../components/input/number-input';
import { assertImported, querySelector } from '../../utils/importer';

export class IntensityInput extends NumberInput {
    private readonly value: HTMLSpanElement;

    public constructor(label: HTMLSpanElement, input: HTMLInputElement, value: HTMLSpanElement) {
        super(label, input);

        this.value = value;
        this.value.textContent = String(this.getValue());

        this.onChange(() => {
            this.value.textContent = String(this.getValue());
        });
    }

    public static from(intensityContainer: HTMLLabelElement|null): IntensityInput|null {
        const label = querySelector<HTMLSpanElement>(intensityContainer, '[data-error-for]');
        const input = querySelector<HTMLInputElement>(intensityContainer, '[data-intensity-range]');
        const value = querySelector<HTMLSpanElement>(intensityContainer, '[data-intensity-value]');

        const required = {
            intensityContainer,
            label,
            input,
            value,
        };

        if (!assertImported('intensity', required)) {
            return null;
        }

        return new IntensityInput(
            required.label,
            required.input,
            required.value,
        );
    }
}
