import { ValidatableSelect } from '../../common/component/select';
import { Mode } from '../mode';
import { ModeSelect } from '../mode-select';

export class ValidatableModeSelect extends ValidatableSelect<Mode|null> {
    public static from(selectContainer: HTMLLabelElement|null): ValidatableModeSelect|null {
        const modeSelect = ModeSelect.from(selectContainer);

        return super.createFrom('mode', selectContainer, modeSelect) as ValidatableModeSelect|null;
    }
}
