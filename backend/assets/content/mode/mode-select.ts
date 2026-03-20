import { Select, ValidatableSelect } from '../../common/component/select';
import { Mode } from './mode';

export class ModeSelect extends Select<Mode|null> {
    public override getValue(): Mode|null {
        if (this.enum === null) {
            return null;
        }

        return new Mode(this.enum.name, this.enum.value);
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
