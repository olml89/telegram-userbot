import { InputComponent } from './input-component';
import { capitalize, toNumber } from '../../utils/strings';

export abstract class NumberInput extends InputComponent<number|null> {
    protected override frontendErrors(): string[] {
        let frontendErrors = super.frontendErrors();

        if (frontendErrors.length > 0) {
            return frontendErrors;
        }

        const value = this.getValue();

        if (value === null) {
            const invalidMessage = `Provide a valid ${this.name()}`;
            frontendErrors.push(invalidMessage);

            return frontendErrors;
        }

        const min = this.min();
        const max = this.max();

        if (min !== null && value < min) {
            const tooLowMessage = `${capitalize(this.name())} cannot be lower than ${min}`;
            frontendErrors.push(tooLowMessage);
        }

        if (max !== null && value > max) {
            const tooHighMessage = `${capitalize(this.name())} cannot be higher than ${max}`;
            frontendErrors.push(tooHighMessage);
        }

        return frontendErrors;
    }

    public override getValue(): number|null {
        return toNumber(this.input.value);
    }

    protected override isEmpty(): boolean {
        return this.getValue() === null;
    }

    protected max(): number|null {
        return toNumber(this.input.max);
    }

    protected min(): number|null {
        return toNumber(this.input.min);
    }

    protected setValue(value: number|null): void {
        this.input.value = value === null ? '' : value.toString();
    }

    protected step(): number|null {
        return toNumber(this.input.step);
    }
}
