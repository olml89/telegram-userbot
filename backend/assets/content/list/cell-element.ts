import { HtmlElementWrapper } from '../../common/component/contracts';

export abstract class CellElement implements HtmlElementWrapper {
    protected readonly cell: HTMLTableCellElement;

    protected constructor () {
        this.cell = document.createElement('td');
    }

    public element(): HTMLTableCellElement {
        return this.cell;
    }
}
