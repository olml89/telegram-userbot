import { CellElement } from './cell-element';
import { Category } from '../category';

export class CategoryPill extends CellElement {
    public constructor(category: Category) {
        super();

        this.cell.appendChild(this.createCategoryPill(category));
    }

    private createCategoryPill(category: Category): HTMLSpanElement {
        const categoryPill = document.createElement('span');
        categoryPill.classList.add('pill');
        categoryPill.textContent = category.name;

        return categoryPill;
    }
}
