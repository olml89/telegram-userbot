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
            Images ${files.types.images} ·
            Videos ${files.types.videos} ·
            Audio ${files.types.audios} ·
            Docs ${files.types.documents}
        `;

        return bundle;
    }
}
