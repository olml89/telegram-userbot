import { BaseComponent } from '../../../components/base-component';
import { ErrorClearable, HtmlElementWrapper } from '../../../components/contracts';
import { File as BackendFile } from '../../file';
import { FileItem } from '../../file-item/file-item';
import { FileAdapterFactory } from '../../file-item/file-metadata';
import { BackendApi, BackendError } from '../../../utils/backend';

export class FileComponent extends BaseComponent<BackendFile> implements ErrorClearable, HtmlElementWrapper {
    private readonly file: BackendFile;
    private readonly fileItem: FileItem;
    private readonly backend: BackendApi = new BackendApi();
    private readonly eventTarget: EventTarget = new EventTarget();

    public constructor(file: BackendFile) {
        super();

        this.file = file;
        this.fileItem = new FileItem(FileAdapterFactory.from(file).metadata());
        this.fileItem.setUploadedState(file);

        this.fileItem.onRemove(async(): Promise<void> => {
            this.clearErrors();
            this.fileItem.setDeletingState();

            this.emit('file-item:delete:begin');

            try {
                await this.backend.deleteFile(this.file);
                this.emit('file-item:removed', this);
            } catch (e: any) {
                const backendError = e as BackendError;
                console.error(backendError.consoleMessage);
                this.fileItem.setDeleteRetryState(backendError);
            } finally {
                this.emit('file-item:delete:end');
            }
        });
    }

    public clearErrors(): void {
        this.errorHandler.clearErrors();
        this.fileItem.clearErrors();
    }

    public override getValue(): BackendFile {
        return this.file;
    }

    public element(): HTMLElement {
        return this.fileItem.element();
    }

    private emit<T>(name: string, detail?: T): void {
        this.eventTarget.dispatchEvent(new CustomEvent(name, { detail }));
    }

    public onDeleteBegin(listener: () => void): void {
        this.eventTarget.addEventListener('file-item:delete:begin', (): void => listener());
    }

    public onDeleteEnd(listener: () => void): void {
        this.eventTarget.addEventListener('file-item:delete:end', (): void => listener());
    }

    public onRemoved(listener: (fileComponent: FileComponent) => void): void {
        this.eventTarget.addEventListener('file-item:removed', (event: Event): void => {
            listener((event as CustomEvent<FileComponent>).detail);
        });
    }
}
