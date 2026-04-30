export class FileActions {
    private readonly wrapper: HTMLDivElement;
    private readonly cancelBtn: HTMLButtonElement;
    private readonly retryBtn: HTMLButtonElement;
    private readonly removeBtn: HTMLButtonElement;

    public constructor() {
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'file-actions';

        this.cancelBtn = document.createElement('button');
        this.cancelBtn.type = 'button';
        this.cancelBtn.classList.add('btn', 'btn-secondary', 'btn-hover-outline', 'btn-sm');
        this.cancelBtn.setAttribute('data-cancel-file', '');
        this.cancelBtn.hidden = true;
        this.cancelBtn.textContent = '✖ Cancel';
        this.wrapper.appendChild(this.cancelBtn);

        this.retryBtn = document.createElement('button');
        this.retryBtn.type = 'button';
        this.retryBtn.classList.add('btn', 'btn-secondary', 'btn-hover-outline', 'btn-sm');
        this.retryBtn.setAttribute('data-retry-file', '');
        this.retryBtn.hidden = true;
        this.retryBtn.textContent = '🔄 Retry';
        this.wrapper.appendChild(this.retryBtn);

        this.removeBtn = document.createElement('button');
        this.removeBtn.type = 'button';
        this.removeBtn.classList.add('btn', 'btn-secondary', 'btn-hover-outline', 'btn-sm');
        this.removeBtn.setAttribute('data-retry-file', '');
        this.removeBtn.hidden = true;
        this.removeBtn.textContent = '🗑️ Remove';
        this.wrapper.appendChild(this.removeBtn);
    }

    public appendTo(fileItemElement: HTMLDivElement): void {
        fileItemElement.appendChild(this.wrapper);
    }

    public onCancel(handler: () => void): void {
        this.cancelBtn.addEventListener('click', handler);
    }

    public onRemove(handler: () => void): void {
        this.removeBtn.addEventListener('click', handler);
    }

    public onRetry(handler: () => void): void {
        this.retryBtn.addEventListener('click', handler);
    }

    public reset(): void {
        this.cancelBtn.hidden = true;
        this.retryBtn.hidden = true;
        this.removeBtn.hidden = true;

        this.cancelBtn.disabled = false;
        this.retryBtn.disabled = false;
        this.removeBtn.disabled = false;
    }

    public showCancel(showCancel: boolean = true): void {
        this.cancelBtn.hidden = !showCancel;
    }

    public showRemove(showRemove: boolean = true): void {
        this.removeBtn.hidden = !showRemove;
    }

    public showRetry(showRetry: boolean = true): void {
        this.retryBtn.hidden = !showRetry;
    }
}
