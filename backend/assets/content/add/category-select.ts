import { ValidatableSelect } from '../../common/component/select';
import { Category } from '../category';
import { CategorySelect } from '../category-select';

export class ValidatableCategorySelect extends ValidatableSelect<Category|null> {
    public static from(selectContainer: HTMLLabelElement|null): ValidatableCategorySelect|null {
        const categorySelect = CategorySelect.from(selectContainer);

        return super.createFrom('category', selectContainer, categorySelect) as ValidatableCategorySelect|null;
    }
}
