import type { HtmlElementWrapper } from '../../common/component/contracts';
import { Size } from './file';

export class FileSize implements HtmlElementWrapper {
    private readonly size: Size;
    private readonly rowElement: HTMLElement;
    private readonly valueElement: HTMLElement;

    public constructor(file: File) {
        this.size = new Size(file.size);

        this.rowElement = document.createElement('div');
        this.rowElement.className = 'file-row';

        const labelElement = document.createElement('span');
        labelElement.className = 'file-label';
        labelElement.textContent = 'Size:';

        this.valueElement = document.createElement('span');
        this.valueElement.className = 'file-value';
        this.valueElement.textContent = this.size.format();

        this.rowElement.appendChild(labelElement);
        this.rowElement.appendChild(this.valueElement);
    }

    public element(): HTMLElement {
        return this.rowElement;
    }

    public update(size: Size): void {
        this.size.set(size.get());
        this.valueElement.textContent = this.size.format();
    }
}
