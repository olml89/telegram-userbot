import { Select } from '../common/component/select';
import { Mode } from './mode';

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
