import { Select } from '../common/component/select';
import { Category } from './category';

export class CategorySelect extends Select<Category|null> {
    public override getValue(): Category|null {
        if (this.enum === null) {
            return null;
        }

        return {
            publicId: this.enum.value,
            name: this.enum.name,
        };
    }

    public static from(selectContainer: HTMLLabelElement|null): CategorySelect|null {
        return super.createFrom('category', selectContainer) as CategorySelect|null;
    }
}
