import { FileContainer } from '../../content';
import { CellElement } from './cell-element';

export class Bundle extends CellElement {
    public constructor(files: FileContainer) {
        super();

        this.cell.appendChild(this.createBundle(files));
    }

    private createBundle(files: FileContainer): HTMLSpanElement {
        const bundle = document.createElement('span');
        bundle.classList.add('bundle');
        bundle.textContent = `
            Images ${files.count.images} ·
            Videos ${files.count.videos} ·
            Audio ${files.count.audios} ·
            Docs ${files.count.documents}
        `;

        return bundle;
    }
}
