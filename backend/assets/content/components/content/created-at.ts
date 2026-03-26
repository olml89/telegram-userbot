import { CellElement } from './cell-element';

export class CreatedAt extends CellElement {
    public constructor(iso8601CreatedAt: string) {
        super();

        this.cell.textContent = this.formatDate(iso8601CreatedAt);
    }

    private formatDate(iso8601CreatedAt: string): string {
        const date = new Date(iso8601CreatedAt);

        return date.toLocaleString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        });
    }
}
