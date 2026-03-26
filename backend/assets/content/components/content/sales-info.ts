import { CellElement } from './cell-element';

export class SalesInfo extends CellElement {
    public constructor(sales: number) {
        super();

        this.cell.textContent = sales.toString();
    }
}
