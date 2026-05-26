import { BusyAware, Errorable, ErrorClearable, HtmlElementWrapper } from '../../components/contracts';
import { FileMetadata } from './file-metadata';
import { FileActions } from './file-actions';
import { FileStatus } from './file-status';
import { File as BackendFile } from '../file';
import { BackendError } from '../../utils/backend';

export class FileItem implements BusyAware, Errorable, ErrorClearable, HtmlElementWrapper {
    private readonly fileItemElement: HTMLDivElement;
    private readonly fileMetadata: FileMetadata;
    private readonly fileActions: FileActions;
    private readonly fileStatus: FileStatus;

    public constructor(fileItemMetadata: FileMetadata) {
        this.fileItemElement = document.createElement('div');
        this.fileItemElement.className = 'file-item';

        this.fileMetadata = fileItemMetadata;
        this.fileMetadata.appendTo(this.fileItemElement);

        this.fileActions = new FileActions();
        this.fileActions.appendTo(this.fileItemElement);

        this.fileStatus = new FileStatus();
        this.fileStatus.appendTo(this.fileItemElement);
    }

    public clearErrors(): void {
        this.fileItemElement.classList.remove('is-error', 'is-warning');
        this.fileStatus.clearErrors();
    }

    public element(): HTMLDivElement {
        return this.fileItemElement;
    }

    public onCancel(handler: () => void): void {
        this.fileActions.onCancel(handler);
    }

    public onRemove(handler: () => void): void {
        this.fileActions.onRemove(handler);
    }

    public onRetry(handler: () => void): void {
        this.fileActions.onRetry(handler);
    }

    public setBusy(isBusy: boolean) {
        this.fileItemElement.classList.toggle('is-disabled', isBusy);
    }

    public setDeletingState(): void {
        this.fileActions.reset();
        this.fileStatus.showMessage('Deleting...');
    }

    public setDeleteRetryState(deleteError: BackendError): void {
        this.fileItemElement.classList.remove('is-error');
        this.fileItemElement.classList.add('is-warning');
        this.fileActions.reset();
        this.fileActions.showRemove();
        this.fileStatus.setWarnings(...deleteError.formatErrors());
    }

    public setErrors(...errorMessages: string[]): void {
        this.fileItemElement.classList.remove('is-warning');
        this.fileItemElement.classList.add('is-error');
        this.fileStatus.setErrors(...errorMessages);
    }

    public setSavingState(): void {
        this.fileActions.reset();
        this.fileStatus.showMessage('Saving file...');
    }

    public setUploadedState(backendFile: BackendFile): void {
        this.fileItemElement.classList.remove('is-error', 'is-warning');
        this.fileMetadata.updateSize(backendFile.size);
        this.fileStatus.hide();
        this.fileActions.reset();
        this.fileActions.showRemove();
    }

    public setUploadingState(): void {
        this.fileActions.reset();
        this.fileActions.showCancel();
        this.fileStatus.showMessage('Uploading file...', true);
    }

    public setUploadErrorState(backendError: BackendError): void {
        this.fileItemElement.classList.remove('is-warning');
        this.fileItemElement.classList.add('is-error');
        this.fileActions.reset();
        this.fileActions.showRemove();
        this.fileActions.showRetry(backendError.isValidationError());
        this.fileStatus.setErrors(...backendError.formatErrors());
    }

    public setValidatingState(): void {
        this.fileActions.reset();
        this.fileActions.showCancel();
        this.fileStatus.showMessage('Validating file...');
    }

    public updateProgress(bytesSent: number, bytesTotal: number, uploadStartedAt: number): void {
        this.fileStatus.showProgress(bytesSent, bytesTotal, uploadStartedAt);
    }
}
