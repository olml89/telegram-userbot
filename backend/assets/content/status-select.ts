import { Select } from '../common/component/select';
import { Status } from './status';

export class StatusSelect extends Select<Status|null> {
    public override getValue(): Status|null {
        return super.getValue() as Status|null;
    }

    public static from(selectContainer: HTMLLabelElement|null): StatusSelect|null {
        return super.createFrom('category', selectContainer) as StatusSelect|null;
    }
}
