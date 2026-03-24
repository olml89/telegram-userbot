import { ValidatableSelect } from '../../common/component/select';
import { Status } from '../status';
import { StatusSelect } from '../status-select';

export class ValidatableStatusSelect extends ValidatableSelect<Status|null> {
    public static from(selectContainer: HTMLLabelElement|null): ValidatableStatusSelect|null {
        const statusSelect = StatusSelect.from(selectContainer);

        return super.createFrom('mode', selectContainer, statusSelect) as ValidatableStatusSelect|null;
    }
}
