import { Select, ValidatableSelect } from '../../components/select/select';
import { Category } from '../category';

export class CategorySelect extends Select<Category|null> {
    public override getValue(): Category|null {
        if (this.selectedOption === null) {
            return null;
        }

        return new Category(
            this.selectedOption.getValue(),
            this.selectedOption.getLabel()
        );
    }

    public static from(selectContainer: HTMLLabelElement|null): CategorySelect|null {
        return super.createFrom('category', selectContainer) as CategorySelect|null;
    }
}

export class ValidatableCategorySelect extends ValidatableSelect<Category|null> {
    public static from(selectContainer: HTMLLabelElement|null): ValidatableCategorySelect|null {
        const categorySelect = CategorySelect.from(selectContainer);

        return super.createFrom('category', selectContainer, categorySelect) as ValidatableCategorySelect|null;
    }
}
