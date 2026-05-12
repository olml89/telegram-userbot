import { ValidatableSelect } from '../../components/select/select';
import { CategorySelect } from '../category-select';
import { Category } from '../category';

export class ValidatableCategorySelect extends ValidatableSelect<Category|null> {
    public static from(selectContainer: HTMLLabelElement|null): ValidatableCategorySelect|null {
        const categorySelect = CategorySelect.from(selectContainer);

        return super.createFrom('category', selectContainer, categorySelect) as ValidatableCategorySelect|null;
    }
}
