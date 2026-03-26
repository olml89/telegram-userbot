import { Select, ValidatableSelect } from '../../components/select/select';
import { Mode } from '../mode';

export class ModeSelect extends Select<Mode|null> {
    public override getValue(): Mode|null {
        if (this.selectedOption === null) {
            return null;
        }

        return new Mode(
            this.selectedOption.getValue(),
            this.selectedOption.getLabel()
        );
    }

    public static from(selectContainer: HTMLLabelElement|null): ModeSelect|null {
        return super.createFrom('mode', selectContainer) as ModeSelect|null;
    }
}

export class ValidatableModeSelect extends ValidatableSelect<Mode|null> {
    public static from(selectContainer: HTMLLabelElement|null): ValidatableModeSelect|null {
        const modeSelect = ModeSelect.from(selectContainer);

        return super.createFrom('mode', selectContainer, modeSelect) as ValidatableModeSelect|null;
    }
}
