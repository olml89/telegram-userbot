import { CustomSelect } from '../../common/component/custom-select';
import { Status } from './status';

export class StatusSelect extends CustomSelect<Status|null> {
    public override getValue(): Status|null {
        return this.enum as Status|null;
    }

    public static from(selectContainer: HTMLLabelElement|null): StatusSelect|null {
        return super.createFrom('mode', selectContainer) as StatusSelect|null;
    }
}
