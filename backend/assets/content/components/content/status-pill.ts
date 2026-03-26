import { CellElement } from './cell-element';
import { Status } from '../../status';

export class StatusPill extends CellElement {
    public constructor(status: Status) {
        super();

        this.cell.appendChild(this.createStatusPill(status));
    }

    private createStatusPill(status: Status): HTMLSpanElement {
        const statusPill = document.createElement('span');
        statusPill.classList.add('pill', `pill-${status.value}`);
        statusPill.textContent = status.name;

        return statusPill;
    }
}
