import { BusyAware, ErrorClearable, HtmlElementWrapper } from '../../../components/contracts';
import { BaseComponent } from '../../../components/base-component';
import { FileStatus } from './file-status';
import { FileAdapter, FileAdapterFactory, FileMetadata } from './file-metadata';
import { File as BackendFile } from '../../file';
import { BackendError } from '../../../utils/backend';

export class FileItem extends BaseComponent<FileAdapter> implements BusyAware, ErrorClearable, HtmlElementWrapper {
    private readonly fileAdapter: FileAdapter;
    private readonly fileElement: HTMLDivElement;
    private readonly fileMetadata: FileMetadata;
    private readonly fileStatus: FileStatus;

    private readonly cancelBtn: HTMLButtonElement;
    private readonly retryBtn: HTMLButtonElement;
    private readonly removeBtn: HTMLButtonElement;

    public constructor(file: File|BackendFile) {
        super();

        this.fileAdapter = FileAdapterFactory.from(file);
        this.fileElement = document.createElement('div');
        this.fileElement.className = 'file-item';

        this.fileMetadata = this.fileAdapter.metadata();
        this.fileMetadata.appendTo(this.fileElement);

        const actions = document.createElement('div');
        actions.className = 'file-actions';
        actions.innerHTML = `
            <button type="button" class="btn btn-secondary btn-hover-outline btn-sm" data-cancel-file hidden>✖ Cancel</button>
            <button type="button" class="btn btn-secondary btn-hover-outline btn-sm" data-retry-file hidden>🔄 Retry</button>
            <button type="button" class="btn btn-secondary btn-hover-outline btn-sm" data-remove-file hidden>🗑️ Remove</button>    
        `;

        this.fileElement.appendChild(actions);

        this.cancelBtn = this.fileElement.querySelector<HTMLButtonElement>('[data-cancel-file]') as HTMLButtonElement;
        this.retryBtn = this.fileElement.querySelector<HTMLButtonElement>('[data-retry-file]') as HTMLButtonElement;
        this.removeBtn = this.fileElement.querySelector<HTMLButtonElement>('[data-remove-file]') as HTMLButtonElement;
        this.fileStatus = new FileStatus(this.fileElement);
    }

    public clearErrors(): void {
        this.fileElement.classList.remove('is-error', 'is-warning');
        this.fileStatus.clearErrors();
    }

    public element(): HTMLDivElement {
        return this.fileElement;
    }

    public override getValue(): FileAdapter {
        return this.fileAdapter;
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

    private resetActions(): void {
        this.cancelBtn.hidden = true;
        this.retryBtn.hidden = true;
        this.removeBtn.hidden = true;

        this.cancelBtn.disabled = false;
        this.retryBtn.disabled = false;
        this.removeBtn.disabled = false;

        this.removeBtn.textContent = '🗑️ Remove';
    }

    public setBusy(isBusy: boolean) {
        this.fileElement.classList.toggle('is-disabled', isBusy);
        this.cancelBtn.disabled = isBusy;
        this.retryBtn.disabled = isBusy;
        this.removeBtn.disabled = isBusy;
    }

    public setDeletingState(): void {
        this.resetActions();
        this.fileStatus.showMessage('Deleting...');
    }

    public setDeleteRetryState(deleteError: BackendError): void {
        this.fileElement.classList.remove('is-error');
        this.fileElement.classList.add('is-warning');
        this.resetActions();
        this.removeBtn.hidden = false;
        this.removeBtn.textContent =  '🗑️ Remove';
        this.fileStatus.setWarnings(...deleteError.formatErrors());
    }

    public override setErrors(...errorMessages: string[]): void {
        this.fileElement.classList.remove('is-warning');
        this.fileElement.classList.add('is-error');
        this.fileStatus.setErrors(...errorMessages);
    }

    public setSavingState(): void {
        this.resetActions();
        this.fileStatus.showMessage('Saving file...');
    }

    public setUploadedState(backendFile: BackendFile): void {
        this.fileElement.classList.remove('is-error', 'is-warning');
        this.fileMetadata.updateSize(backendFile.size);
        this.fileStatus.hide();
        this.showUploadedActions(true);
    }

    public setUploadingState(): void {
        this.resetActions();
        this.cancelBtn.hidden = false;
        this.fileStatus.showMessage('Uploading file...', true);
    }

    public setUploadErrorState(backendError: BackendError): void {
        this.fileElement.classList.remove('is-warning');
        this.fileElement.classList.add('is-error');
        this.resetActions();

        this.removeBtn.hidden = false;
        this.removeBtn.textContent = '🗑️ Remove';

        if (!backendError.isValidationError()) {
            this.retryBtn.hidden = false;
        }

        this.fileStatus.setErrors(...backendError.formatErrors());
    }

    public setValidatingState(): void {
        this.resetActions();
        this.cancelBtn.hidden = false;
        this.fileStatus.showMessage('Validating file...');
    }

    private showUploadedActions(showRemove: boolean): void {
        this.resetActions();

        if (showRemove) {
            this.removeBtn.hidden = false;
        }
    }

    public updateProgress(bytesSent: number, bytesTotal: number, uploadStartedAt: number): void {
        this.fileStatus.showProgress(bytesSent, bytesTotal, uploadStartedAt);
    }
}
