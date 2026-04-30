import { BusyAware, ErrorClearable, HtmlElementWrapper } from '../../../components/contracts';
import { BaseComponent } from '../../../components/base-component';
import { FileStatus } from '../../components/file/file-status';
import { FileAdapter, FileAdapterFactory, FileMetadata } from '../../components/file/file-metadata';
import { File as BackendFile } from '../../file';
import { BackendError } from '../../../utils/backend';

export class FileItem extends BaseComponent<FileAdapter> implements BusyAware, ErrorClearable, HtmlElementWrapper {
    private readonly fileAdapter: FileAdapter;
    private readonly fileElement: HTMLDivElement;
    private readonly fileMetadata: FileMetadata;
    private readonly fileStatus: FileStatus;

    private readonly removeBtn: HTMLButtonElement;

    public constructor(file: BackendFile) {
        super();

        this.fileAdapter = FileAdapterFactory.from(file);
        this.fileElement = document.createElement('div');
        this.fileElement.className = 'file-item';

        this.fileMetadata = this.fileAdapter.metadata();
        this.fileMetadata.appendTo(this.fileElement);

        const actions = document.createElement('div');
        actions.className = 'file-actions';
        actions.innerHTML = `
            <button type="button" class="btn btn-secondary btn-hover-outline btn-sm" data-remove-file>🗑️ Remove</button>    
        `;

        this.fileElement.appendChild(actions);
        this.removeBtn = this.fileElement.querySelector<HTMLButtonElement>('[data-remove-file]') as HTMLButtonElement;
        this.fileStatus = new FileStatus(this.fileElement);
    }

    public clearErrors(): void {
        this.fileElement.classList.remove('is-warning');
        this.fileStatus.clearErrors();
    }

    public element(): HTMLDivElement {
        return this.fileElement;
    }

    public override getValue(): FileAdapter {
        return this.fileAdapter;
    }

    public onRemove(handler: () => void): void {
        this.removeBtn.addEventListener('click', handler);
    }

    public setBusy(isBusy: boolean) {
        this.fileElement.classList.toggle('is-disabled', isBusy);
        this.removeBtn.disabled = isBusy;
    }

    public setDeleteRetryState(deleteError: BackendError): void {
        this.fileElement.classList.add('is-warning');
        this.fileStatus.setWarnings(...deleteError.formatErrors());
        this.setBusy(false);
    }

    public setDeletingState(): void {
        this.fileStatus.showMessage('Deleting...');
        this.setBusy(true);
    }
}
