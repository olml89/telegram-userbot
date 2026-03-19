import { CustomSelect } from '../../common/component/custom-select';
import { Mode } from './mode';

export class ModeSelect extends CustomSelect<Mode|null> {
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
