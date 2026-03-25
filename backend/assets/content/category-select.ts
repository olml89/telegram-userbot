import { Select } from '../common/component/select';
import { Category } from './category';

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
