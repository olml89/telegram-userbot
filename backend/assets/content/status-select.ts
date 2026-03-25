import { Select } from '../common/component/select';
import { Status } from './status';

export class StatusSelect extends Select<Status|null> {
    public override getValue(): Status|null {
        if (this.selectedOption === null) {
            return null;
        }

        return new Status(
            this.selectedOption.getValue(),
            this.selectedOption.getLabel()
        );
    }

    public static from(selectContainer: HTMLLabelElement|null): StatusSelect|null {
        return super.createFrom('category', selectContainer) as StatusSelect|null;
    }
}
