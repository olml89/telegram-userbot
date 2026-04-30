import { CellElement } from './cell-element';
import { LocalDate } from '../../../components/local-date';

export class CreatedAt extends CellElement {
    public constructor(iso8601CreatedAt: string) {
        super();

        this.cell.textContent = new LocalDate(iso8601CreatedAt).format();
    }
}
