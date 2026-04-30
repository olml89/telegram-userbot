import { ValidatableSelect } from '../../components/select/select';
import { ModeSelect } from '../mode-select';
import { Mode } from '../mode';

export class ValidatableModeSelect extends ValidatableSelect<Mode|null> {
    public static from(selectContainer: HTMLLabelElement|null): ValidatableModeSelect|null {
        const modeSelect = ModeSelect.from(selectContainer);

        return super.createFrom('mode', selectContainer, modeSelect) as ValidatableModeSelect|null;
    }
}
